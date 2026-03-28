<?php

namespace App\Http\Controllers;

use App\Models\AccountHolding;
use App\Models\AllocatedBar;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'metal_type_id' => 'required|exists:metal_types,id',
            'storage_type' => 'required|in:allocated,unallocated',
            'quantity_kg' => 'required|numeric|min:0.01',
            'allocated_bar_ids' => 'nullable|array',
            'allocated_bar_ids.*' => 'integer|exists:allocated_bars,id',
        ]);

        $quantityKg = (float) $validated['quantity_kg'];
        $selectedBarIds = collect($validated['allocated_bar_ids'] ?? [])->map(fn($id) => (int) $id)->values();

        DB::transaction(function () use ($validated, $quantityKg, $selectedBarIds): void {
            $holding = AccountHolding::query()
                ->where('account_id', $validated['account_id'])
                ->where('metal_type_id', $validated['metal_type_id'])
                ->where('storage_type', $validated['storage_type'])
                ->lockForUpdate()
                ->first();

            if (!$holding || (float) $holding->balance_kg < $quantityKg) {
                throw ValidationException::withMessages([
                    'quantity_kg' => 'Insufficient balance for this withdrawal.',
                ]);
            }

            if ($validated['storage_type'] === 'allocated') {
                if ($selectedBarIds->isEmpty()) {
                    throw ValidationException::withMessages([
                        'allocated_bar_ids' => 'Please select at least one allocated bar.',
                    ]);
                }

                $selectedBars = AllocatedBar::query()
                    ->whereIn('id', $selectedBarIds->all())
                    ->where('account_id', $validated['account_id'])
                    ->where('metal_type_id', $validated['metal_type_id'])
                    ->where('status', 'allocated')
                    ->lockForUpdate()
                    ->get();

                if ($selectedBars->count() !== $selectedBarIds->count()) {
                    throw ValidationException::withMessages([
                        'allocated_bar_ids' => 'One or more selected bars are not available.',
                    ]);
                }

                $selectedWeight = (float) $selectedBars->sum('weight_kg');
                if (abs($selectedWeight - $quantityKg) > 0.01) {
                    throw ValidationException::withMessages([
                        'quantity_kg' => 'Quantity must match selected bar weight total.',
                    ]);
                }
            }

            $withdrawal = Withdrawal::query()->create([
                'withdrawal_number' => $this->generateWithdrawalNumber(),
                'account_id' => $validated['account_id'],
                'metal_type_id' => $validated['metal_type_id'],
                'storage_type' => $validated['storage_type'],
                'quantity_kg' => number_format($quantityKg, 2, '.', ''),
            ]);

            $newBalance = max(0, (float) $holding->balance_kg - $quantityKg);
            $holding->update([
                'balance_kg' => number_format($newBalance, 2, '.', ''),
            ]);

            if ($validated['storage_type'] === 'allocated' && $selectedBarIds->isNotEmpty()) {
                AllocatedBar::query()
                    ->whereIn('id', $selectedBarIds->all())
                    ->update([
                        'status' => 'unallocated',
                        'withdrawal_id' => $withdrawal->id,
                    ]);
            }
        });

        return redirect()->back()->with('success', 'Withdrawal created successfully.');
    }

    private function generateWithdrawalNumber(): string
    {
        do {
            $withdrawalNumber = 'WD-' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Withdrawal::where('withdrawal_number', $withdrawalNumber)->exists());

        return $withdrawalNumber;
    }

    /**
     * Display the specified resource.
     */
    public function show(Withdrawal $withdrawal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Withdrawal $withdrawal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Withdrawal $withdrawal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Withdrawal $withdrawal)
    {
        //
    }
}
