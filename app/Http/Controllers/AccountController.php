<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Support\TransactionDetailPayload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = Account::query()->with('customer', 'holding.metalType')->latest();

        if ($q !== '') {
            $pattern = $this->caseInsensitiveLike($q);
            $query->where(function (Builder $w) use ($pattern): void {
                $w->whereRaw('LOWER(account_number) LIKE ?', [$pattern])
                    ->orWhereHas('customer', function (Builder $c) use ($pattern): void {
                        $c->whereRaw('LOWER(full_name) LIKE ?', [$pattern]);
                    });
            });
        }

        return view('account.index', [
            'accounts' => $query->paginate(10)->withQueryString(),
            'filters' => [
                'q' => $q,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        $account->load([
            'customer',
            'holding.metalType',
            'deposit.metalType',
            'withdrawal.metalType',
        ]);

        $isInstitutional = $account->customer->customer_type === 'Institutional';
        if ($isInstitutional) {
            $account->load([
                'deposit.allocatedBar',
                'withdrawal.allocatedBars',
            ]);
        }

        $holdingRows = $account->holding
            ->map(function ($holding) {
                $pricePerKg = (float) ($holding->metalType?->current_price_per_kg ?? 0);
                $balanceKg  = (float) $holding->balance_kg;

                return [
                    'metal' => $holding->metalType?->name ?? '-',
                    'storage_type' => $holding->storage_type,
                    'balance_kg' => $balanceKg,
                    'price_per_kg' => $pricePerKg,
                    'value' => $balanceKg * $pricePerKg,
                ];
            })
            ->values();

        $totalPortfolioValue = (float) $holdingRows->sum('value');

        $depositTransactionDetails = [];
        $withdrawalTransactionDetails = [];
        if ($isInstitutional) {
            foreach ($account->deposit as $deposit) {
                $depositTransactionDetails[$deposit->id] = TransactionDetailPayload::forDeposit($deposit);
            }
            foreach ($account->withdrawal as $withdrawal) {
                $withdrawalTransactionDetails[$withdrawal->id] = TransactionDetailPayload::forWithdrawal($withdrawal);
            }
        }

        return view('account.show', [
            'account'                      => $account,
            'holdingRows'                  => $holdingRows,
            'totalPortfolioValue'          => $totalPortfolioValue,
            'isInstitutional'              => $isInstitutional,
            'depositTransactionDetails'    => $depositTransactionDetails,
            'withdrawalTransactionDetails' => $withdrawalTransactionDetails,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        //
    }
}
