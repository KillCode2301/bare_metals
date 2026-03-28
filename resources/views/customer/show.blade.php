<x-layout>

    <div class="admin-stack">
        <header class="admin-header">
            <div>
                <h1 class="page-title">{{ $customer->full_name }}</h1>
                <p class="page-subtitle">Customer profile and custody activity overview.</p>
            </div>

            <a href="{{ route('customers.index') }}" class="btn-ghost">Back to Customers</a>
        </header>

        <section aria-label="Customer summary" class="dashboard-kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Account Number</div>
                <div class="kpi-value">{{ $customer->account->account_number }}</div>
                <div class="kpi-meta">Custody account reference</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Customer Type</div>
                <div class="kpi-value">{{ $customer->customer_type }}</div>
                <div class="kpi-meta">Classification for compliance profile</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Portfolio Value</div>
                <div class="kpi-value">${{ number_format($totalPortfolioValue, 2) }}</div>
                <div class="kpi-meta">Current valuation across holdings</div>
            </div>
        </section>

        <section aria-label="Customer details" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Customer Details</div>
                    <div class="panel-subtitle">Basic customer information and account status.</div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" class="btn-primary" data-open-deposit-modal>Deposit</button>
                    <button type="button" class="btn-ghost" data-open-withdrawal-modal>Withdraw</button>
                </div>
            </div>

            <div class="form-shell">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="rounded-xl border border-(--border) bg-white/90 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-(--muted)">Email</div>
                        <div class="mt-1 text-sm font-semibold text-(--text)">{{ $customer->email }}</div>
                    </div>
                    <div class="rounded-xl border border-(--border) bg-white/90 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-(--muted)">Primary Contact</div>
                        <div class="mt-1 text-sm font-semibold text-(--text)">{{ $customer->full_name }}</div>
                    </div>
                    <div class="rounded-xl border border-(--border) bg-white/90 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-(--muted)">Created Date</div>
                        <div class="mt-1 text-sm font-semibold text-(--text)">
                            {{ \Carbon\Carbon::parse($customer->created_at)->format('M d, Y') }}
                        </div>
                    </div>
                    <div class="rounded-xl border border-(--border) bg-white/90 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-(--muted)">Status</div>
                        <div class="mt-1">
                            <span class="badge {{ $isActiveCustomer ? 'badge--success' : 'badge--danger' }}">
                                <span
                                    class="badge-dot {{ $isActiveCustomer ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                {{ $customerStatusLabel }}
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
                            <th>Pool %</th>
                            <th class="num">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($holdingRows as $row)
                            <tr>
                                <td class="font-semibold">{{ $row['metal'] }}</td>
                                <td><span class="pill">{{ $row['storage_type'] }}</span></td>
                                <td>{{ number_format($row['balance_kg'], 2) }} kg</td>
                                <td>
                                    @if($row['pool_pct'] !== null)
                                        {{ number_format($row['pool_pct'], 2) }}%
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="num font-semibold">${{ number_format($row['value'], 2) }}</td>
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
                                <td class="font-semibold">{{ $row['metal'] }}</td>
                                <td><span class="pill">{{ $row['storage_type'] }}</span></td>
                                <td>{{ $row['quantity_kg'] }} kg</td>
                                <td class="num">{{ \Carbon\Carbon::parse($row['date'])->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    @php
        $normalizedCustomerType = strtolower(trim((string) $customer->customer_type));
        $enforcedStorageType = $normalizedCustomerType === 'institutional' ? 'allocated' : 'unallocated';
    @endphp

    <x-deposit-modal :account="$customer->account" :customer="$customer" :metal-types="$metalTypes" :enforced-storage-type="$enforcedStorageType" />
    <x-withdrawal-modal :account="$customer->account" :customer="$customer" :metal-types="$metalTypes" :enforced-storage-type="$enforcedStorageType" />

</x-layout>
