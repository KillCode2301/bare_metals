<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountHolding;
use App\Models\Customer;
use App\Models\MetalType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $customerType = $request->query('customer_type', 'all');
        // Same pattern as transactions: only allow known customer_type filter values.
        if (! in_array($customerType, ['all', 'Retail', 'Institutional'], true)) {
            $customerType = 'all';
        }

        $query = Customer::query()->with('account')->latest();

        if ($customerType !== 'all') {
            $query->where('customer_type', $customerType);
        }

        if ($q !== '') {
            $pattern = $this->caseInsensitiveLike($q);
            $query->where(function (Builder $w) use ($pattern): void {
                $w->whereRaw('LOWER(full_name) LIKE ?', [$pattern])
                    ->orWhereHas('account', function (Builder $a) use ($pattern): void {
                        $a->whereRaw('LOWER(account_number) LIKE ?', [$pattern]);
                    });
            });
        }

        return view('customer.index', [
            'customers' => $query->paginate(10)->withQueryString(),
            'filters' => [
                'q' => $q,
                'customer_type' => $customerType,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'customer_type' => 'required|string|in:Retail,Institutional',
        ]);

        $customer = Customer::create($validated);

        $customer->account()->create([
            'account_number' => $this->generateAccountNumber(),
        ]);

        // Feedback if successful or not
        if ($customer) {
            return redirect()->route('customers.index')->with('success', 'Customer created successfully');
        } else {
            return redirect()->route('customers.create')->with('error', 'Failed to create customer');
        }
    }

    // Function to generate the account number
    private function generateAccountNumber(): string
    {
        do {
            $accountNumber = 'ACC-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Account::where('account_number', $accountNumber)->exists());

        return $accountNumber;
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer->load([
            'account.holding.metalType',
            'account.deposit.metalType',
            'account.withdrawal.metalType',
            'account.allocatedBar',
        ]);

        $metalTypes = MetalType::query()->orderBy('name')->get(['id', 'name']);

        $isRetail = $customer->customer_type === 'Retail';

        // Retail-only: total unallocated kg across all retail accounts per metal, for "share of pool" on customer page.
        $unallocatedPoolTotals = $isRetail
            ? AccountHolding::query()
                ->where('storage_type', 'unallocated')
                ->whereHas('account.customer', fn ($q) => $q->where('customer_type', 'Retail'))
                ->selectRaw('metal_type_id, SUM(balance_kg) as total_kg')
                ->groupBy('metal_type_id')
                ->pluck('total_kg', 'metal_type_id')
            : collect();

        $holdingRows = $customer->account->holding
            ->map(function ($holding) use ($isRetail, $unallocatedPoolTotals) {
                $pricePerKg = (float) ($holding->metalType?->current_price_per_kg ?? 0);
                $balanceKg = (float) $holding->balance_kg;

                $totalPoolKg = ($isRetail && $holding->storage_type === 'unallocated')
                    ? (float) ($unallocatedPoolTotals[$holding->metal_type_id] ?? 0)
                    : 0;

                // pool_pct = this account's unallocated balance as % of aggregate retail unallocated pool for that metal.
                $poolPct = ($isRetail && $holding->storage_type === 'unallocated' && $totalPoolKg > 0)
                    ? ($balanceKg / $totalPoolKg) * 100
                    : null;

                return [
                    'metal' => $holding->metalType?->name ?? '-',
                    'storage_type' => $holding->storage_type,
                    'balance_kg' => $balanceKg,
                    'price_per_kg' => $pricePerKg,
                    'value' => $balanceKg * $pricePerKg,
                    'pool_pct' => $poolPct,
                ];
            })
            ->values();

        $totalPortfolioValue = (float) $holdingRows->sum('value');

        // Normalize legacy status strings so UI shows Active/Inactive consistently.
        $customerStatusRaw = strtolower(trim((string) ($customer->status ?? 'active')));
        $isActiveCustomer = in_array($customerStatusRaw, ['active', '1', 'true'], true);
        $customerStatusLabel = $isActiveCustomer ? 'Active' : 'Inactive';

        $recentActivities = collect()
            ->concat(
                $customer->account->deposit->map(function ($deposit) {
                    return [
                        'type' => 'Deposit',
                        'metal' => $deposit->metalType?->name ?? '-',
                        'storage_type' => $deposit->storage_type,
                        'quantity_kg' => $deposit->quantity_kg,
                        'date' => $deposit->created_at,
                    ];
                })
            )
            ->concat(
                $customer->account->withdrawal->map(function ($withdrawal) {
                    return [
                        'type' => 'Withdrawal',
                        'metal' => $withdrawal->metalType?->name ?? '-',
                        'storage_type' => $withdrawal->storage_type,
                        'quantity_kg' => $withdrawal->quantity_kg,
                        'date' => $withdrawal->created_at,
                    ];
                })
            )
            ->sortByDesc('date')
            ->values();

        return view('customer.show', [
            'customer' => $customer,
            'metalTypes' => $metalTypes,
            'holdingRows' => $holdingRows,
            'totalPortfolioValue' => $totalPortfolioValue,
            'customerStatusLabel' => $customerStatusLabel,
            'isActiveCustomer' => $isActiveCustomer,
            'recentActivities' => $recentActivities,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
