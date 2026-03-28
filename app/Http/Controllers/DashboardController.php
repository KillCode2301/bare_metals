<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountHolding;
use App\Models\Deposit;
use App\Models\MetalType;
use App\Models\Withdrawal;

class DashboardController extends Controller
{
    /** @var list<string> */
    private const MIX_PALETTE = ['#c9a227', '#64748b', '#ea580c', '#238a57', '#0d9488', '#6366f1'];

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

        $dashboardChartData = $this->buildDashboardChartData();

        // Recent Activity
        $deposits = Deposit::with(['account.customer', 'metalType'])->latest()->take(5)->get()->map(function ($deposit) {
            return [
                'type' => 'Deposit',
                'account' => $deposit->account->customer->full_name . ' — ' . $deposit->account->account_number,
                'metal' => $deposit->metalType->name,
                'storage_type' => $deposit->storage_type,
                'quantity_kg' => $deposit->quantity_kg,
                'created_at' => $deposit->created_at,
            ];
        });

        $withdrawals = Withdrawal::with(['account.customer', 'metalType'])->latest()->take(5)->get()->map(function ($withdrawal) {
            return [
                'type' => 'Withdrawal',
                'account' => $withdrawal->account->customer->full_name . ' — ' . $withdrawal->account->account_number,
                'metal' => $withdrawal->metalType->name,
                'storage_type' => $withdrawal->storage_type,
                'quantity_kg' => $withdrawal->quantity_kg,
                'created_at' => $withdrawal->created_at,
            ];
        });
        $recentActivities = $deposits->concat($withdrawals)->sortByDesc('created_at')->take(5);

        return view('dashboard', [
            'totalPortfolioValue' => $totalPortfolioValue,
            'totalGoldHoldings' => $totalGoldHoldings,
            'totalAccounts' => $totalAccounts,
            'assetBreakdown' => $assetBreakdown,
            'recentActivities' => $recentActivities,
            'dashboardChartData' => $dashboardChartData,
        ]);
    }

    /**
     * Build chart payloads from account_holdings: storage split (allocated vs unallocated)
     * and per-metal value inside each bucket. Value = sum(balance_kg * current_price_per_kg).
     */
    private function buildDashboardChartData(): array
    {
        $rows = AccountHolding::query()
            ->join('metal_types', 'metal_types.id', '=', 'account_holdings.metal_type_id')
            ->selectRaw(
                'account_holdings.storage_type, account_holdings.metal_type_id, metal_types.name as metal_name, metal_types.current_price_per_kg, SUM(account_holdings.balance_kg) as total_kg'
            )
            ->groupBy(
                'account_holdings.storage_type',
                'account_holdings.metal_type_id',
                'metal_types.name',
                'metal_types.current_price_per_kg'
            )
            ->get();

        $byStorageValue = ['allocated' => 0.0, 'unallocated' => 0.0];
        $byStorageKg = ['allocated' => 0.0, 'unallocated' => 0.0];
        $allocatedMetal = [];
        $unallocatedMetal = [];

        foreach ($rows as $row) {
            $kg = (float) $row->total_kg;
            if ($kg <= 0) {
                continue;
            }
            $price = (float) $row->current_price_per_kg;
            $value = $kg * $price;
            $st = $row->storage_type;

            $byStorageKg[$st] += $kg;
            $byStorageValue[$st] += $value;

            $entry = [
                'label' => (string) $row->metal_name,
                'kg' => $kg,
                'value' => $value,
            ];

            if ($st === 'allocated') {
                $allocatedMetal[] = $entry;
            } else {
                $unallocatedMetal[] = $entry;
            }
        }

        $sortDesc = static fn(array $a, array $b): int => $b['value'] <=> $a['value'];

        usort($allocatedMetal, $sortDesc);
        usort($unallocatedMetal, $sortDesc);

        return [
            'storageSplit' => [
                'labels' => ['Retail', 'Institutional'],
                'values' => [
                    round($byStorageValue['allocated'], 2),
                    round($byStorageValue['unallocated'], 2),
                ],
                'kgs' => [
                    round($byStorageKg['allocated'], 4),
                    round($byStorageKg['unallocated'], 4),
                ],
                'colors' => ['#c9a227', '#64748b'],
            ],
            'allocatedMetal' => $this->metalSeriesPayload($allocatedMetal),
            'unallocatedMetal' => $this->metalSeriesPayload($unallocatedMetal),
            'metalColors' => self::MIX_PALETTE,
        ];
    }

    /**
     * @param  list<array{label: string, kg: float, value: float}>  $items
     * @return array{labels: list<string>, values: list<float>, kgs: list<float>}
     */

    // Turns sorted metal rows into parallel label/value/kg arrays for the front-end chart.
    private function metalSeriesPayload(array $items): array
    {
        $labels = [];
        $values = [];
        $kgs = [];

        foreach ($items as $item) {
            $labels[] = $item['label'];
            $values[] = round($item['value'], 2);
            $kgs[] = round($item['kg'], 4);
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'kgs' => $kgs,
        ];
    }
}
