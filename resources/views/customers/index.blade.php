<x-layout>
    @php
        $customers = [
            [
                'name' => 'Amara Holdings',
                'email' => 'ops@amaraholdings.com',
                'type' => 'Institutional',
                'account' => 'AC-104593',
                'created' => '2026-01-08',
            ],
            [
                'name' => 'Nora Bennett',
                'email' => 'nora.bennett@email.com',
                'type' => 'Retail',
                'account' => 'AC-208114',
                'created' => '2026-01-23',
            ],
            [
                'name' => 'Kestrel Capital',
                'email' => 'custody@kestrelcap.io',
                'type' => 'Institutional',
                'account' => 'AC-880431',
                'created' => '2026-02-01',
            ],
            [
                'name' => 'Mateo Silva',
                'email' => 'mateo.silva@email.com',
                'type' => 'Retail',
                'account' => 'AC-562201',
                'created' => '2026-02-14',
            ],
            [
                'name' => 'Summit Bullion Partners',
                'email' => 'support@summitbullion.com',
                'type' => 'Institutional',
                'account' => 'AC-776029',
                'created' => '2026-03-04',
            ],
        ];
    @endphp

    <div class="admin-stack">
        <header class="admin-header">
            <div>
                <h1 class="page-title">Customers</h1>
                <p class="page-subtitle">Manage account holders and onboarding details.</p>
            </div>

            <a href="{{ route('customers.create') }}" class="btn-primary">
                <span aria-hidden="true">+</span>
                Add Customer
            </a>
        </header>

        <section aria-label="Customers table" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Customer Directory</div>
                    <div class="panel-subtitle">All registered retail and institutional customers.</div>
                </div>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr class="text-left">
                            <th>Name</th>
                            <th>Email</th>
                            <th>Customer Type</th>
                            <th>Account Number</th>
                            <th>Created Date</th>
                            <th class="num">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td class="font-semibold">{{ $customer['name'] }}</td>
                                <td>{{ $customer['email'] }}</td>
                                <td>
                                    <span class="pill">{{ $customer['type'] }}</span>
                                </td>
                                <td class="font-medium">{{ $customer['account'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($customer['created'])->format('M d, Y') }}</td>
                                <td class="num">
                                    <a href="{{ route('customers.show', ['customer' => $customer['account']]) }}"
                                        class="btn-ghost">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layout>
