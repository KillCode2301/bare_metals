<x-layout>
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
                                <td class="font-semibold">{{ $customer->full_name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>
                                    <span class="pill">{{ $customer->customer_type }}</span>
                                </td>
                                <td class="font-medium">{{ $customer->account->account_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($customer->created_at)->format('M d, Y') }}</td>
                                <td class="num">
                                    <a href="{{ route('customers.show', ['customer' => $customer->id]) }}"
                                        class="btn-ghost">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($customers->hasPages())
                {{-- Custom pagination links here --}}
                <div class="panel-pagination">
                    {{ $customers->links('components.pagination-links') }}
                </div>
            @endif
        </section>
    </div>
</x-layout>
