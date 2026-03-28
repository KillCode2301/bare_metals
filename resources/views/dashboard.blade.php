<x-layout>
    @php
        $kpis = [
            [
                'label' => 'Total Portfolio Value',
                'value' => '$' . number_format($totalPortfolioValue, 0),
                'sub' => 'Across all accounts',
            ],
            [
                'label' => 'Total Gold Holdings',
                'value' => number_format($totalGoldHoldings, 2) . ' kg',
                'sub' => 'Allocated + Unallocated',
            ],
            [
                'label' => 'Total Accounts',
                'value' => $totalAccounts,
                'sub' => 'Active custody accounts',
            ],
        ];

        $money = function ($n) {
            return '$' . number_format($n, 0);
        };
        $kg = function ($n) {
            return number_format($n, 2) . ' kg';
        };

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
                                <path d="M5 15.5V18a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5M7.5 11.5l3 3 6-7"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        <div id="dashboard-charts-root" class="dashboard-charts-stack">
            <section aria-label="Holdings by storage and metal" class="dashboard-section">
                @php
                    $chartData = $dashboardChartData;
                    $storageVals = $chartData['storageSplit']['values'];
                    $storageKgs = $chartData['storageSplit']['kgs'];
                    $storageTotalVal = array_sum($storageVals);
                    $allocVal = $storageVals[0] ?? 0;
                    $unallocVal = $storageVals[1] ?? 0;
                    $allocKg = $storageKgs[0] ?? 0;
                    $unallocKg = $storageKgs[1] ?? 0;
                    $hasAllocatedMetal = count($chartData['allocatedMetal']['labels']) > 0;
                    $hasUnallocatedMetal = count($chartData['unallocatedMetal']['labels']) > 0;
                @endphp

                <div class="dashboard-chart-grid dashboard-chart-grid--split">
                    <div class="panel dashboard-chart-card">
                        <div class="panel-header">
                            <div>
                                <div class="panel-title">Retail vs Institutional</div>
                                <div class="panel-subtitle">
                                    Total holdings by custody type (value).
                                    @if ($storageTotalVal > 0)
                                        Allocated {{ $money($allocVal) }}, {{ $kg($allocKg) }} ·
                                        Unallocated {{ $money($unallocVal) }}, {{ $kg($unallocKg) }}.
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-chart-body">
                            @if ($storageTotalVal > 0)
                                <div class="dashboard-chart-canvas-wrap">
                                    <canvas id="chart-storage-split"
                                        aria-label="Share of portfolio value by storage type"></canvas>
                                </div>
                            @else
                                <p class="dashboard-chart-empty">No holdings yet. This chart will show allocated vs
                                    unallocated value once accounts hold metal.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="dashboard-chart-grid dashboard-chart-grid--pair">
                    <div class="panel dashboard-chart-card">
                        <div class="panel-header">
                            <div>
                                <div class="panel-title">Institutional — metal mix</div>
                                <div class="panel-subtitle">Share of value within institutional custody only.</div>
                            </div>
                        </div>
                        <div class="dashboard-chart-body">
                            @if ($hasAllocatedMetal)
                                <div class="dashboard-chart-canvas-wrap">
                                    <canvas id="chart-allocated-metal"
                                        aria-label="Allocated holdings by metal"></canvas>
                                </div>
                            @else
                                <p class="dashboard-chart-empty">No allocated holdings.</p>
                            @endif
                        </div>
                    </div>
                    <div class="panel dashboard-chart-card">
                        <div class="panel-header">
                            <div>
                                <div class="panel-title">Retail — metal mix</div>
                                <div class="panel-subtitle">Share of value within retail custody only.</div>
                            </div>
                        </div>
                        <div class="dashboard-chart-body">
                            @if ($hasUnallocatedMetal)
                                <div class="dashboard-chart-canvas-wrap">
                                    <canvas id="chart-unallocated-metal"
                                        aria-label="Unallocated holdings by metal"></canvas>
                                </div>
                            @else
                                <p class="dashboard-chart-empty">No unallocated holdings.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <script type="application/json" id="dashboard-chart-data">@json($dashboardChartData)</script>
            </section>
        </div>

        <section aria-label="Asset Breakdown" class="dashboard-section">
            @php
                $mixPalette = ['#c9a227', '#64748b', '#ea580c', '#238a57', '#0d9488', '#6366f1'];
                $mixRows = $assetBreakdown
                    ->values()
                    ->map(function ($m, $idx) use ($mixPalette) {
                        $value = $m->totalKg * $m->current_price_per_kg;
                        return [
                            'metal' => $m,
                            'value' => $value,
                            'color' => $mixPalette[$idx % count($mixPalette)],
                        ];
                    });
                $mixTotal = $mixRows->sum('value');
                $mixRows = $mixRows->map(function ($row) use ($mixTotal) {
                    $row['pct'] = $mixTotal > 0 ? round(($row['value'] / $mixTotal) * 100, 1) : 0.0;

                    return $row;
                });
                $grandTotal = $mixTotal;
            @endphp

            <div class="panel">
                <div class="panel-header">
                    <div>
                        <div class="panel-title">Asset Breakdown</div>
                        <div class="panel-subtitle">Metals held in custody, weight, valuation, and share of portfolio.
                        </div>
                    </div>
                </div>

                @if ($mixRows->isEmpty())
                    <p class="asset-mix-empty">No metal holdings yet. Portfolio composition will appear here once
                        accounts hold balances.</p>
                @else
                    <div class="asset-mix">
                        <div class="asset-mix-label">Composition by value</div>
                        <div class="asset-mix-bar" role="img"
                            aria-label="Portfolio share by metal: @foreach ($mixRows as $r) {{ $r['metal']->name }} {{ $r['pct'] }} percent. @endforeach">
                            @foreach ($mixRows as $r)
                                @if ($r['pct'] > 0)
                                    <span class="asset-mix-segment"
                                        style="width: {{ $r['pct'] }}%; background: {{ $r['color'] }};"
                                        title="{{ $r['metal']->name }} — {{ $r['pct'] }}%"></span>
                                @endif
                            @endforeach
                        </div>
                        <div class="asset-mix-legend">
                            @foreach ($mixRows as $r)
                                <div class="asset-mix-legend-item">
                                    <span class="asset-mix-swatch" style="background: {{ $r['color'] }};"></span>
                                    <span class="font-medium">{{ $r['metal']->name }}</span>
                                    <span class="text-muted">{{ $r['pct'] }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr class="text-left">
                                    <th>Metal</th>
                                    <th>Total Quantity (kg)</th>
                                    <th>Current Price / kg</th>
                                    <th class="num">Share</th>
                                    <th class="num">Total Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mixRows as $r)
                                    @php
                                        $metal = $r['metal'];
                                        $rowTotal = $r['value'];
                                    @endphp
                                    <tr>
                                        <td class="font-semibold">
                                            <div class="flex items-center gap-2">
                                                <span class="h-2 w-2 rounded-full shrink-0"
                                                    style="background: {{ $r['color'] }};"></span>
                                                {{ $metal->name }}
                                            </div>
                                        </td>
                                        <td>{{ $kg($metal->totalKg) }}</td>
                                        <td>{{ $money($metal->current_price_per_kg) }}</td>
                                        <td class="num">{{ $r['pct'] }}%</td>
                                        <td class="num font-semibold">{{ $money($rowTotal) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="font-semibold text-muted">Total portfolio (metals)</td>
                                    <td class="num font-semibold">{{ $money($grandTotal) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
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
                            @foreach ($recentActivities as $row)
                                @php
                                    $isDeposit = $row['type'] === 'Deposit';
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge {{ $isDeposit ? 'badge--success' : 'badge--danger' }}">
                                            <span
                                                class="badge-dot {{ $isDeposit ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                            {{ $row['type'] }}
                                        </span>
                                    </td>
                                    <td class="font-semibold">{{ $row['account'] }}</td>
                                    <td>{{ $row['metal'] }}</td>
                                    <td><span class="pill">{{ $row['storage_type'] ?? '-' }}</span></td>
                                    <td>{{ $kg($row['quantity_kg']) }}</td>
                                    <td class="num">
                                        {{ \Carbon\Carbon::parse($row['created_at'])->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</x-layout>
