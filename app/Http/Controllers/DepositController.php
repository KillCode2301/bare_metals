<?php

namespace App\Http\Controllers;

use App\Models\AccountHolding;
use App\Models\AllocatedBar;
use App\Models\Deposit;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DepositController extends Controller
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
            'bars' => 'nullable|array',
            'bars.*.serial_number' => 'nullable|string|max:255|distinct',
            'bars.*.weight_kg' => 'nullable|numeric|min:0.01',
        ]);

        // Normalize bar rows: trim serials, drop empties so validation and weight totals match what the user actually submitted.
        $quantityKg = (float) $validated['quantity_kg'];
        $bars = collect($validated['bars'] ?? [])
            ->map(function (array $bar): array {
                return [
                    'serial_number' => trim((string) ($bar['serial_number'] ?? '')),
                    'weight_kg' => (float) ($bar['weight_kg'] ?? 0),
                ];
            })
            ->filter(fn(array $bar): bool => $bar['serial_number'] !== '' && $bar['weight_kg'] > 0)
            ->values();

        // All balance and bar writes succeed or fail together; prevents half-recorded deposits under concurrency.
        DB::transaction(function () use ($validated, $quantityKg, $bars): void {
            if ($validated['storage_type'] === 'allocated') {
                if ($bars->isEmpty()) {
                    throw ValidationException::withMessages([
                        'bars' => 'At least one bar is required for allocated deposits.',
                    ]);
                }

                // Server-side guard: bar weights must equal deposit quantity within rounding tolerance (must match withdrawal checks).
                $barsWeight = (float) $bars->sum('weight_kg');
                if (abs($barsWeight - $quantityKg) > 0.01) {
                    throw ValidationException::withMessages([
                        'quantity_kg' => 'Quantity must match total allocated bar weight.',
                    ]);
                }

                // Global uniqueness: a serial can only exist once across all allocated bars.
                $duplicateSerial = AllocatedBar::query()
                    ->whereIn('serial_number', $bars->pluck('serial_number')->all())
                    ->exists();

                if ($duplicateSerial) {
                    throw ValidationException::withMessages([
                        'bars' => 'One or more bar serial numbers already exist.',
                    ]);
                }
            }

            $deposit = Deposit::query()->create([
                'deposit_number' => $this->generateDepositNumber(),
                'account_id' => $validated['account_id'],
                'metal_type_id' => $validated['metal_type_id'],
                'storage_type' => $validated['storage_type'],
                'quantity_kg' => number_format($quantityKg, 2, '.', ''),
            ]);

            // Row-level lock avoids two concurrent deposits racing the same holding balance.
            $holding = AccountHolding::query()
                ->where('account_id', $validated['account_id'])
                ->where('metal_type_id', $validated['metal_type_id'])
                ->where('storage_type', $validated['storage_type'])
                ->lockForUpdate()
                ->first();

            if ($holding) {
                $holding->update([
                    'balance_kg' => number_format((float) $holding->balance_kg + $quantityKg, 2, '.', ''),
                ]);
            } else {
                // First holding for this account+metal+storage bucket: create row instead of assuming it exists.
                AccountHolding::query()->create([
                    'account_id' => $validated['account_id'],
                    'metal_type_id' => $validated['metal_type_id'],
                    'storage_type' => $validated['storage_type'],
                    'balance_kg' => number_format($quantityKg, 2, '.', ''),
                ]);
            }

            if ($validated['storage_type'] === 'allocated' && $bars->isNotEmpty()) {
                $bars->each(function (array $bar) use ($validated, $deposit): void {
                    AllocatedBar::query()->create([
                        'deposit_id' => $deposit->id,
                        'account_id' => $validated['account_id'],
                        'metal_type_id' => $validated['metal_type_id'],
                        'serial_number' => $bar['serial_number'],
                        'weight_kg' => number_format($bar['weight_kg'], 2, '.', ''),
                        'status' => 'allocated',
                    ]);
                });
            }
        });

        return redirect()->back()->with('success', 'Deposit created successfully.');
    }

    private function generateDepositNumber(): string
    {
        // Collision-safe reference: retry if random DP-###### already exists in DB.
        do {
            $depositNumber = 'DP-' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Deposit::where('deposit_number', $depositNumber)->exists());

        return $depositNumber;
    }

    /**
     * Display the specified resource.
     */
    public function show(Deposit $deposit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Deposit $deposit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Deposit $deposit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deposit $deposit)
    {
        //
    }
}
