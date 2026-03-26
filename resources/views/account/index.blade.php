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
                        @foreach ($accounts as $account)
                            <tr>
                                <td class="font-semibold">{{ $account->account_number }}</td>
                                <td>{{ $account->customer->full_name }}</td>
                                <td class="font-semibold">${{ number_format($account->holding->sum('value'), 2) }}</td>
                                <td class="num">
                                    <a href="{{ route('accounts.show', ['account' => $account->id]) }}" class="btn-ghost">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
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
