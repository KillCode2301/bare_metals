<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\AccountHolding;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\MetalType;

class DashboardController extends Controller
{
    public function index()
    {

        // KPI 1: Total Portfolio Value
        $totalPortfolioValue = AccountHolding::with('metalType')->get()->sum(function ($holding) {
            return $holding->balance_kg * $holding->metalType->current_price_per_kg;
        });

        // KPI 2: Total Gold Holdings
        $goldId = MetalType::where('name', 'Gold')->value('id');
        $totalGoldHoldings = AccountHolding::where('metal_type_id', $goldId)->sum('balance_kg');

        // KPI 3: Total Accounts
        $totalAccounts = Account::with('customer')->get()->count();

        // Asset Breakdown
        $assetBreakdown = MetalType::withSum('holding as totalKg', 'balance_kg')->get()->filter(function ($metal) {
            return $metal->totalKg > 0;
        });

        // Recent Activity
        $deposits = Deposit::with(['account.customer', 'metalType'])->latest()->take(5)->get()->map(function ($deposit) {
            return [
                'type'    => 'Deposit',
                'account' => $deposit->account->customer->full_name . ' — ' . $deposit->account->account_number,
                'metal'   => $deposit->metalType->name,
                'storage' => $deposit->storage_type,
                'qtyKg'   => $deposit->quantity_kg,
                'date'    => $deposit->created_at,
            ];
        });

        $withdrawals = Withdrawal::with(['account.customer', 'metalType'])->latest()->take(5)->get()->map(function ($withdrawal) {
            return [
                'type'    => 'Withdrawal',
                'account' => $withdrawal->account->customer->full_name . ' — ' . $withdrawal->account->account_number,
                'metal'   => $withdrawal->metalType->name,
                'storage' => $withdrawal->storage_type,
                'qtyKg'   => $withdrawal->quantity_kg,
                'date'    => $withdrawal->created_at,
            ];
        });
        $recentActivity = $deposits->concat($withdrawals)->sortByDesc('date')->take(5);

        return view('dashboard', [
            'totalPortfolioValue' => $totalPortfolioValue,
            'totalGoldHoldings' => $totalGoldHoldings,
            'totalAccounts' => $totalAccounts,
            'assetBreakdown' => $assetBreakdown,
            'recentActivity' => $recentActivity,
        ]);
    }
}
