<x-layout>
    @php
        $accounts = [
            [
                'account' => 'AC-104593',
                'customer' => 'Amara Holdings',
                'portfolioValue' => 423600,
            ],
            [
                'account' => 'AC-208114',
                'customer' => 'Nora Bennett',
                'portfolioValue' => 186200,
            ],
            [
                'account' => 'AC-880431',
                'customer' => 'Kestrel Capital',
                'portfolioValue' => 734900,
            ],
            [
                'account' => 'AC-562201',
                'customer' => 'Mateo Silva',
                'portfolioValue' => 129700,
            ],
            [
                'account' => 'AC-776029',
                'customer' => 'Summit Bullion Partners',
                'portfolioValue' => 508450,
            ],
        ];

        $money = fn ($n) => '$' . number_format($n, 0);
    @endphp

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
                        @foreach ($accounts as $row)
                            <tr>
                                <td class="font-semibold">{{ $row['account'] }}</td>
                                <td>{{ $row['customer'] }}</td>
                                <td class="font-semibold">{{ $money($row['portfolioValue']) }}</td>
                                <td class="num">
                                    <a href="{{ route('accounts.show', ['account' => $row['account']]) }}" class="btn-ghost">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layout>
