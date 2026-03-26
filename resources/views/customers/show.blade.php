<x-layout>
    @php
        $customerId = request()->route('customer');

        $customers = [
            [
                'name' => 'Amara Holdings',
                'email' => 'ops@amaraholdings.com',
                'type' => 'Institutional',
                'account' => 'AC-104593',
                'created' => '2026-01-08',
                'status' => 'Active',
                'primaryContact' => 'Maya Johnson',
            ],
            [
                'name' => 'Nora Bennett',
                'email' => 'nora.bennett@email.com',
                'type' => 'Retail',
                'account' => 'AC-208114',
                'created' => '2026-01-23',
                'status' => 'Active',
                'primaryContact' => 'Nora Bennett',
            ],
            [
                'name' => 'Kestrel Capital',
                'email' => 'custody@kestrelcap.io',
                'type' => 'Institutional',
                'account' => 'AC-880431',
                'created' => '2026-02-01',
                'status' => 'Review',
                'primaryContact' => 'Ethan Cole',
            ],
            [
                'name' => 'Mateo Silva',
                'email' => 'mateo.silva@email.com',
                'type' => 'Retail',
                'account' => 'AC-562201',
                'created' => '2026-02-14',
                'status' => 'Active',
                'primaryContact' => 'Mateo Silva',
            ],
            [
                'name' => 'Summit Bullion Partners',
                'email' => 'support@summitbullion.com',
                'type' => 'Institutional',
                'account' => 'AC-776029',
                'created' => '2026-03-04',
                'status' => 'Active',
                'primaryContact' => 'Emma Patel',
            ],
        ];

        $customer = collect($customers)->firstWhere('account', $customerId) ?? $customers[0];

        $holdings = [
            ['metal' => 'Gold', 'storage' => 'Allocated', 'qtyKg' => 4.50, 'value' => 288000],
        ];

        $activity = [
            ['type' => 'Deposit', 'metal' => 'Gold', 'storage' => 'Allocated', 'qtyKg' => 1.00, 'date' => '2026-03-21'],
            ['type' => 'Withdrawal', 'metal' => 'Silver', 'storage' => 'Unallocated', 'qtyKg' => 8.00, 'date' => '2026-03-18'],
            ['type' => 'Deposit', 'metal' => 'Platinum', 'storage' => 'Allocated', 'qtyKg' => 0.30, 'date' => '2026-03-14'],
        ];

        $money = fn ($n) => '$' . number_format($n, 0);
        $kg = fn ($n) => number_format($n, 2) . ' kg';
        $portfolioValue = collect($holdings)->sum('value');
    @endphp

    <div class="admin-stack">
        <header class="admin-header">
            <div>
                <h1 class="page-title">{{ $customer['name'] }}</h1>
                <p class="page-subtitle">Customer profile and custody activity overview.</p>
            </div>

            <a href="{{ route('customers') }}" class="btn-ghost">Back to Customers</a>
        </header>

        <section aria-label="Customer summary" class="dashboard-kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Account Number</div>
                <div class="kpi-value">{{ $customer['account'] }}</div>
                <div class="kpi-meta">Custody account reference</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Customer Type</div>
                <div class="kpi-value">{{ $customer['type'] }}</div>
                <div class="kpi-meta">Classification for compliance profile</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Portfolio Value</div>
                <div class="kpi-value">{{ $money($portfolioValue) }}</div>
                <div class="kpi-meta">Current valuation across holdings</div>
            </div>
        </section>

        <section aria-label="Customer details" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Customer Details</div>
                    <div class="panel-subtitle">Basic customer information and account status.</div>
                </div>
            </div>

            <div class="form-shell">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="rounded-xl border border-(--border) bg-white/90 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-(--muted)">Email</div>
                        <div class="mt-1 text-sm font-semibold text-(--text)">{{ $customer['email'] }}</div>
                    </div>
                    <div class="rounded-xl border border-(--border) bg-white/90 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-(--muted)">Primary Contact</div>
                        <div class="mt-1 text-sm font-semibold text-(--text)">{{ $customer['primaryContact'] }}</div>
                    </div>
                    <div class="rounded-xl border border-(--border) bg-white/90 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-(--muted)">Created Date</div>
                        <div class="mt-1 text-sm font-semibold text-(--text)">
                            {{ \Carbon\Carbon::parse($customer['created'])->format('M d, Y') }}
                        </div>
                    </div>
                    <div class="rounded-xl border border-(--border) bg-white/90 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-(--muted)">Status</div>
                        <div class="mt-1">
                            <span
                                class="badge {{ $customer['status'] === 'Active' ? 'badge--success' : 'badge--danger' }}">
                                <span
                                    class="badge-dot {{ $customer['status'] === 'Active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                {{ $customer['status'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section aria-label="Holdings" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Holdings</div>
                    <div class="panel-subtitle">Current metal balances for this customer.</div>
                </div>
            </div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr class="text-left">
                            <th>Metal</th>
                            <th>Storage Type</th>
                            <th>Quantity (kg)</th>
                            <th class="num">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($holdings as $row)
                            <tr>
                                <td class="font-semibold">{{ $row['metal'] }}</td>
                                <td><span class="pill">{{ $row['storage'] }}</span></td>
                                <td>{{ $kg($row['qtyKg']) }}</td>
                                <td class="num font-semibold">{{ $money($row['value']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section aria-label="Recent customer activity" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Recent Activity</div>
                    <div class="panel-subtitle">Latest custody transactions for this customer.</div>
                </div>
            </div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr class="text-left">
                            <th>Type</th>
                            <th>Metal</th>
                            <th>Storage Type</th>
                            <th>Quantity (kg)</th>
                            <th class="num">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activity as $row)
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
                                <td class="font-semibold">{{ $row['metal'] }}</td>
                                <td><span class="pill">{{ $row['storage'] }}</span></td>
                                <td>{{ $kg($row['qtyKg']) }}</td>
                                <td class="num">{{ \Carbon\Carbon::parse($row['date'])->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layout>
