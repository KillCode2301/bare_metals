<x-layout>
    @php
        $kg = function ($n) {
            return number_format($n, 2) . ' kg';
        };
    @endphp

    <div class="admin-stack">
        <header class="admin-header">
            <div>
                <h1 class="page-title">Transactions</h1>
                <p class="page-subtitle">See the different types of transactions that have been stored in the system.</p>
            </div>
        </header>

        <section aria-label="Transactions table" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Transaction History</div>
                    <div class="panel-subtitle">View all transactions for the system.</div>
                </div>
            </div>

            <div class="form-shell border-b border-[var(--border)]">
                <form method="get" action="{{ route('transactions.index') }}"
                    class="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-end">
                    <div class="field w-full min-w-0 lg:w-44">
                        <label for="filter-type">Transaction type</label>
                        <select id="filter-type" name="type">
                            <option value="all" @selected($filters['type'] === 'all')>All</option>
                            <option value="deposit" @selected($filters['type'] === 'deposit')>Deposits only</option>
                            <option value="withdrawal" @selected($filters['type'] === 'withdrawal')>Withdrawals only</option>
                        </select>
                    </div>
                    <div class="field min-w-0 flex-1 lg:min-w-[14rem]">
                        <label for="filter-q">Search</label>
                        <input id="filter-q" type="search" name="q" value="{{ $filters['q'] }}"
                            placeholder="Account name, number, reference, metal…" autocomplete="off">
                    </div>
                    <div class="field min-w-0 flex-1 lg:min-w-[12rem]">
                        <label for="filter-bar-serial">Bar serial</label>
                        <input id="filter-bar-serial" type="search" name="bar_serial"
                            value="{{ $filters['bar_serial'] }}" placeholder="Allocated bar serial"
                            autocomplete="off">
                    </div>
                    <div class="field-actions flex flex-wrap gap-2 pb-0.5">
                        <button type="submit" class="btn-primary">Search</button>
                        <a href="{{ route('transactions.index') }}" class="btn-ghost">Clear</a>
                    </div>
                </form>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr class="text-left">
                            <th>Date</th>
                            <th>Type</th>
                            <th>Account</th>
                            <th>Metal</th>
                            <th>Storage Type</th>
                            <th>Quantity (kg)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $txn)
                            @php
                                $isDeposit = $txn['is_deposit'];
                            @endphp
                            <tr>
                                <td class="num">{{ $txn['date_display'] }}</td>
                                <td>
                                    <span class="badge {{ $isDeposit ? 'badge--success' : 'badge--danger' }}">
                                        <span
                                            class="badge-dot {{ $isDeposit ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        {{ $txn['type_label'] }}
                                    </span>
                                </td>
                                <td class="font-semibold">{{ $txn['account_summary'] }}</td>
                                <td>{{ $txn['metal'] }}</td>
                                <td><span class="pill">{{ $txn['storage_type'] }}</span></td>
                                <td>{{ $kg($txn['quantity_kg']) }}</td>
                                <td>
                                    <button type="button" class="btn-ghost"
                                        data-transaction-detail='@json($txn['detail'])'>
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-[var(--muted)] py-8">
                                    @if ($filters['q'] !== '' || $filters['bar_serial'] !== '' || $filters['type'] !== 'all')
                                        No transactions match your filters.
                                    @else
                                        No transactions yet.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <x-transaction-detail-modal />
</x-layout>
