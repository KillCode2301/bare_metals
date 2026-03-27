<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('account.index', [
            'accounts' => Account::with('customer', 'holding.metalType')->latest()->paginate(10),
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

        $holdingRows = $account->holding
            ->map(function ($holding) {
                $pricePerKg = (float) ($holding->metalType?->current_price_per_kg ?? 0);
                $balanceKg  = (float) $holding->balance_kg;

                return [
                    'metal'        => $holding->metalType?->name ?? '-',
                    'storage_type' => $holding->storage_type,
                    'balance_kg'   => $balanceKg,
                    'price_per_kg' => $pricePerKg,
                    'value'        => $balanceKg * $pricePerKg,
                ];
            })
            ->values();

        $totalPortfolioValue = (float) $holdingRows->sum('value');

        return view('account.show', [
            'account'             => $account,
            'holdingRows'         => $holdingRows,
            'totalPortfolioValue' => $totalPortfolioValue,
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
