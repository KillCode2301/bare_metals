<x-layout>
    @php
        $money = fn($n) => '$' . number_format($n, 0);
        $kg = fn($n) => number_format($n, 2) . ' kg';
    @endphp

    <div class="admin-stack">
        <header class="admin-header">
            <div>
                <h1 class="page-title">Account {{ $account->account_number }}</h1>
                <p class="page-subtitle">{{ $account->customer->full_name }} • Created
                    {{ \Carbon\Carbon::parse($account->created_at)->format('M d, Y') }}</p>
            </div>

            <a href="{{ route('accounts') }}" class="btn-ghost">Back to Accounts</a>
        </header>

        <section aria-label="Account summary" class="dashboard-kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Customer</div>
                <div class="kpi-value">{{ $account->customer->full_name }}</div>
                <div class="kpi-meta">Account holder</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Account Number</div>
                <div class="kpi-value">{{ $account->account_number }}</div>
                <div class="kpi-meta">Custody reference</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Total Portfolio Value</div>
                <div class="kpi-value">{{ $money($totalPortfolioValue) }}</div>
                <div class="kpi-meta">Current estimated valuation</div>
            </div>
        </section>

        <section aria-label="Portfolio Breakdown" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Portfolio Breakdown</div>
                    <div class="panel-subtitle">Metal balances by storage type and market value.</div>
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
                        @foreach ($holdingRows as $row)
                            <tr>
                                <td class="font-semibold">{{ $row['metal'] }}</td>
                                <td><span class="pill">{{ $row['storage_type'] }}</span></td>
                                <td>{{ $kg($row['balance_kg']) }}</td>
                                <td>{{ $money($row['price_per_kg']) }}</td>
                                <td class="num font-semibold">{{ $money($row['value']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="font-semibold text-(--muted)">Total Portfolio</td>
                            <td class="num font-semibold">{{ $money($totalPortfolioValue) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <section aria-label="Account actions" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Account Deposits</div>
                    <div class="panel-subtitle">View all deposits for this account.</div>
                </div>
            </div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr class="text-left">
                            <th>Date</th>
                            <th>Metal</th>
                            <th>Storage Type</th>
                            <th>Quantity (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($account->deposit as $deposit)
                            <tr>
                                <td>{{ $deposit->created_at->format('M d, Y') }}</td>
                                <td>{{ $deposit->metalType->name }}</td>
                                <td>{{ $deposit->storage_type }}</td>
                                <td>{{ $deposit->quantity_kg }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        </section>

        <section aria-label="Account actions" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Account Withdrawals</div>
                    <div class="panel-subtitle">View all withdrawals for this account.</div>
                </div>
            </div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr class="text-left">
                            <th>Date</th>
                            <th>Metal</th>
                            <th>Storage Type</th>
                            <th>Quantity (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($account->withdrawal as $withdrawal)
                            <tr>
                                <td>{{ $withdrawal->created_at->format('M d, Y') }}</td>
                                <td>{{ $withdrawal->metalType->name }}</td>
                                <td>{{ $withdrawal->storage_type }}</td>
                                <td>{{ $withdrawal->quantity_kg }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layout>
