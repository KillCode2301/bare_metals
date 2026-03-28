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

            <div class="form-shell border-b border-[var(--border)]">
                <form method="get" action="{{ route('customers.index') }}"
                    class="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-end">
                    <div class="field min-w-0 flex-1 lg:min-w-[14rem]">
                        <label for="customer-filter-q">Search</label>
                        <input id="customer-filter-q" type="search" name="q" value="{{ $filters['q'] }}"
                            placeholder="Customer name or account number…" autocomplete="off">
                    </div>
                    <div class="field w-full min-w-0 lg:w-48">
                        <label for="customer-filter-type">Customer type</label>
                        <select id="customer-filter-type" name="customer_type">
                            <option value="all" @selected($filters['customer_type'] === 'all')>All types</option>
                            <option value="Retail" @selected($filters['customer_type'] === 'Retail')>Retail</option>
                            <option value="Institutional" @selected($filters['customer_type'] === 'Institutional')>Institutional</option>
                        </select>
                    </div>
                    <div class="field-actions flex flex-wrap gap-2 pb-0.5">
                        <button type="submit" class="btn-primary">Search</button>
                        <a href="{{ route('customers.index') }}" class="btn-ghost">Clear</a>
                    </div>
                </form>
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
                        @forelse ($customers as $customer)
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
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-[var(--muted)]">
                                    @if ($filters['q'] !== '' || $filters['customer_type'] !== 'all')
                                        No customers match your filters.
                                    @else
                                        No customers yet.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
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
