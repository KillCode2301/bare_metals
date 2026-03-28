<x-layout>
    <div class="admin-stack">
        <header class="admin-header">
            <div>
                <h1 class="page-title">Accounts</h1>
                <p class="page-subtitle">All customer custody accounts and their current valuation.</p>
            </div>
        </header>

        <section aria-label="Accounts table" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Account Portfolio Overview</div>
                    <div class="panel-subtitle">Browse each account and view detailed metal holdings.</div>
                </div>
            </div>

            <div class="form-shell border-b border-[var(--border)]">
                <form method="get" action="{{ route('accounts.index') }}"
                    class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
                    <div class="field min-w-0 flex-1 sm:min-w-[16rem]">
                        <label for="account-filter-q">Search</label>
                        <input id="account-filter-q" type="search" name="q" value="{{ $filters['q'] }}"
                            placeholder="Account number or customer name…" autocomplete="off">
                    </div>
                    <div class="field-actions flex flex-wrap gap-2 pb-0.5">
                        <button type="submit" class="btn-primary">Search</button>
                        <a href="{{ route('accounts.index') }}" class="btn-ghost">Clear</a>
                    </div>
                </form>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr class="text-left">
                            <th>Account Number</th>
                            <th>Customer Name</th>
                            <th>Total Portfolio Value</th>
                            <th class="num">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($accounts as $account)
                            <tr>
                                <td class="font-semibold">{{ $account->account_number }}</td>
                                <td>{{ $account->customer->full_name }}</td>
                                <td class="font-semibold">${{ number_format($account->holding->sum(fn($h) => $h->balance_kg * ($h->metalType?->current_price_per_kg ?? 0)), 2) }}</td>
                                <td class="num">
                                    <a href="{{ route('accounts.show', ['account' => $account->id]) }}" class="btn-ghost">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-[var(--muted)]">
                                    @if ($filters['q'] !== '')
                                        No accounts match your search.
                                    @else
                                        No accounts yet.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($accounts->hasPages())
                <div class="panel-pagination">
                    {{ $accounts->links('components.pagination-links') }}
                </div>
            @endif
        </section>
    </div>
</x-layout>
