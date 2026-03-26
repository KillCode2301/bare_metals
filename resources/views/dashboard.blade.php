<x-layout>
    @php
        $kpis = [
            [
                'label' => 'Total Portfolio Value',
                'value' => '$120,000',
                'sub' => 'Across all accounts',
            ],
            [
                'label' => 'Total Gold Holdings',
                'value' => '18.75 kg',
                'sub' => 'Allocated + Unallocated',
            ],
            [
                'label' => 'Total Accounts',
                'value' => '12',
                'sub' => 'Active custody accounts',
            ],
        ];

        $assetBreakdown = [
            [
                'metal' => 'Gold',
                'qtyKg' => 18.75,
                'pricePerKg' => 64000,
            ],
        ];

        $recentActivity = [
            [
                'type' => 'Deposit',
                'account' => 'Vault Account — 0012',
                'metal' => 'Gold',
                'storage' => 'Allocated',
                'qtyKg' => 1.25,
                'date' => '2026-03-24',
            ],
            [
                'type' => 'Deposit',
                'account' => 'Vault Account — 0003',
                'metal' => 'Gold',
                'storage' => 'Allocated',
                'qtyKg' => 0.80,
                'date' => '2026-03-18',
            ],
            [
                'type' => 'Withdrawal',
                'account' => 'Vault Account — 0001',
                'metal' => 'Gold',
                'storage' => 'Allocated',
                'qtyKg' => 0.50,
                'date' => '2026-03-16',
            ],
        ];

        $money = fn ($n) => '$' . number_format($n, 0);
        $kg = fn ($n) => number_format($n, 2) . ' kg';
    @endphp

    <div class="dashboard-stack">
        <header class="dashboard-header flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p class="page-subtitle">Digital asset custody overview and recent movements.</p>
            </div>

            <div class="flex items-center gap-2">
                <div class="chip">
                    <span class="chip-dot"></span>
                    Market snapshot: Today
                </div>
            </div>
        </header>

        <section aria-label="Summary" class="dashboard-kpi-grid">
            @foreach ($kpis as $kpi)
                <div class="kpi-card">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="kpi-label">{{ $kpi['label'] }}</div>
                            <div class="kpi-value">{{ $kpi['value'] }}</div>
                            <div class="kpi-meta">{{ $kpi['sub'] }}</div>
                        </div>

                        <div class="kpi-icon">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true">
                                <path
                                    d="M5 15.5V18a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5M7.5 11.5l3 3 6-7"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        <section aria-label="Asset Breakdown" class="dashboard-section">
            <div class="panel">
                <div class="panel-header">
                    <div>
                        <div class="panel-title">Asset Breakdown</div>
                        <div class="panel-subtitle">Metals held in custody and current valuation.</div>
                    </div>
                </div>

                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr class="text-left">
                                <th>Metal</th>
                                <th>Total Quantity (kg)</th>
                                <th>Current Price / kg</th>
                                <th class="num">Total Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assetBreakdown as $row)
                                @php
                                    $total = $row['qtyKg'] * $row['pricePerKg'];
                                @endphp
                                <tr>
                                    <td class="font-semibold">
                                        <div class="flex items-center gap-2">
                                            <span class="h-2 w-2 rounded-full bg-brand-600"></span>
                                            {{ $row['metal'] }}
                                        </div>
                                    </td>
                                    <td>{{ $kg($row['qtyKg']) }}</td>
                                    <td>{{ $money($row['pricePerKg']) }}</td>
                                    <td class="num font-semibold">{{ $money($total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @php
                                $grand = collect($assetBreakdown)->reduce(fn($c, $r) => $c + ($r['qtyKg'] * $r['pricePerKg']), 0);
                            @endphp
                            <tr>
                                <td colspan="3" class="font-semibold text-muted">Total portfolio (metals)</td>
                                <td class="num font-semibold">{{ $money($grand) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>

        <section aria-label="Recent Activity" class="dashboard-section">
            <div class="panel">
                <div class="panel-header">
                    <div>
                        <div class="panel-title">Recent Activity</div>
                        <div class="panel-subtitle">Latest deposits and withdrawals across accounts.</div>
                    </div>
                </div>

                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr class="text-left">
                                <th>Type</th>
                                <th>Account</th>
                                <th>Metal</th>
                                <th>Storage Type</th>
                                <th>Quantity (kg)</th>
                                <th class="num">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentActivity as $row)
                                @php
                                    $isDeposit = $row['type'] === 'Deposit';
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge {{ $isDeposit ? 'badge--success' : 'badge--danger' }}">
                                            <span class="badge-dot {{ $isDeposit ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                            {{ $row['type'] }}
                                        </span>
                                    </td>
                                    <td class="font-semibold">{{ $row['account'] }}</td>
                                    <td>{{ $row['metal'] }}</td>
                                    <td><span class="pill">{{ $row['storage'] }}</span></td>
                                    <td>{{ $kg($row['qtyKg']) }}</td>
                                    <td class="num">{{ \Carbon\Carbon::parse($row['date'])->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</x-layout>