<x-layout>
    @php
        $accountId = request()->route('account');

        $accounts = [
            [
                'account' => 'AC-104593',
                'customer' => 'Amara Holdings',
                'created' => '2026-01-08',
                'portfolio' => [
                    ['metal' => 'Gold', 'storage' => 'Allocated', 'balanceKg' => 4.5, 'pricePerKg' => 64000],
                    ['metal' => 'Silver', 'storage' => 'Unallocated', 'balanceKg' => 120.0, 'pricePerKg' => 820],
                    ['metal' => 'Platinum', 'storage' => 'Allocated', 'balanceKg' => 1.2, 'pricePerKg' => 31000],
                ],
            ],
            [
                'account' => 'AC-208114',
                'customer' => 'Nora Bennett',
                'created' => '2026-01-23',
                'portfolio' => [
                    ['metal' => 'Gold', 'storage' => 'Allocated', 'balanceKg' => 1.8, 'pricePerKg' => 64000],
                    ['metal' => 'Silver', 'storage' => 'Allocated', 'balanceKg' => 72.0, 'pricePerKg' => 820],
                    ['metal' => 'Platinum', 'storage' => 'Unallocated', 'balanceKg' => 0.5, 'pricePerKg' => 31000],
                ],
            ],
            [
                'account' => 'AC-880431',
                'customer' => 'Kestrel Capital',
                'created' => '2026-02-01',
                'portfolio' => [
                    ['metal' => 'Gold', 'storage' => 'Allocated', 'balanceKg' => 8.2, 'pricePerKg' => 64000],
                    ['metal' => 'Silver', 'storage' => 'Unallocated', 'balanceKg' => 185.0, 'pricePerKg' => 820],
                    ['metal' => 'Platinum', 'storage' => 'Allocated', 'balanceKg' => 1.9, 'pricePerKg' => 31000],
                ],
            ],
            [
                'account' => 'AC-562201',
                'customer' => 'Mateo Silva',
                'created' => '2026-02-14',
                'portfolio' => [
                    ['metal' => 'Gold', 'storage' => 'Allocated', 'balanceKg' => 1.1, 'pricePerKg' => 64000],
                    ['metal' => 'Silver', 'storage' => 'Allocated', 'balanceKg' => 52.0, 'pricePerKg' => 820],
                    ['metal' => 'Platinum', 'storage' => 'Unallocated', 'balanceKg' => 0.4, 'pricePerKg' => 31000],
                ],
            ],
            [
                'account' => 'AC-776029',
                'customer' => 'Summit Bullion Partners',
                'created' => '2026-03-04',
                'portfolio' => [
                    ['metal' => 'Gold', 'storage' => 'Allocated', 'balanceKg' => 6.1, 'pricePerKg' => 64000],
                    ['metal' => 'Silver', 'storage' => 'Unallocated', 'balanceKg' => 96.0, 'pricePerKg' => 820],
                    ['metal' => 'Platinum', 'storage' => 'Allocated', 'balanceKg' => 1.3, 'pricePerKg' => 31000],
                ],
            ],
        ];

        $account = collect($accounts)->firstWhere('account', $accountId) ?? $accounts[0];
        $money = fn($n) => '$' . number_format($n, 0);
        $kg = fn($n) => number_format($n, 2) . ' kg';

        $portfolioRows = collect($account['portfolio'])->map(function ($row) {
            $row['totalValue'] = $row['balanceKg'] * $row['pricePerKg'];
            return $row;
        });
        $totalPortfolio = $portfolioRows->sum('totalValue');
    @endphp

    <div class="admin-stack">
        <header class="admin-header">
            <div>
                <h1 class="page-title">Account {{ $account['account'] }}</h1>
                <p class="page-subtitle">{{ $account['customer'] }} • Created
                    {{ \Carbon\Carbon::parse($account['created'])->format('M d, Y') }}</p>
            </div>

            <a href="{{ route('accounts') }}" class="btn-ghost">Back to Accounts</a>
        </header>

        <section aria-label="Account summary" class="dashboard-kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Customer</div>
                <div class="kpi-value">{{ $account['customer'] }}</div>
                <div class="kpi-meta">Account holder</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Account Number</div>
                <div class="kpi-value">{{ $account['account'] }}</div>
                <div class="kpi-meta">Custody reference</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Total Portfolio Value</div>
                <div class="kpi-value">{{ $money($totalPortfolio) }}</div>
                <div class="kpi-meta">Current estimated valuation</div>
            </div>
        </section>

        <section aria-label="Portfolio Breakdown" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Portfolio Breakdown</div>
                    <div class="panel-subtitle">Metal balances by storage type and market value.</div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" class="btn-primary" data-open-deposit-modal>Deposit</button>
                    <button type="button" class="btn-ghost" data-open-withdrawal-modal>Withdraw</button>
                </div>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr class="text-left">
                            <th>Metal</th>
                            <th>Storage Type</th>
                            <th>Balance (kg)</th>
                            <th>Price per kg</th>
                            <th class="num">Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($portfolioRows as $row)
                            <tr>
                                <td class="font-semibold">{{ $row['metal'] }}</td>
                                <td><span class="pill">{{ $row['storage'] }}</span></td>
                                <td>{{ $kg($row['balanceKg']) }}</td>
                                <td>{{ $money($row['pricePerKg']) }}</td>
                                <td class="num font-semibold">{{ $money($row['totalValue']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="font-semibold text-(--muted)">Total Portfolio</td>
                            <td class="num font-semibold">{{ $money($totalPortfolio) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>
    </div>
</x-layout>
