<x-layout>
    <div class="admin-stack">
        <header class="admin-header">
            <div>
                <h1 class="page-title">Add Customer</h1>
                <p class="page-subtitle">Create a new customer profile for custody operations.</p>
            </div>

            <a href="{{ route('customers') }}" class="btn-ghost">Back</a>
        </header>

        <section aria-label="Add customer form" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Customer Details</div>
                    <div class="panel-subtitle">Enter basic information to create a customer record.</div>
                </div>
            </div>

            <div class="form-shell">
                <form class="form-grid" action="#">
                    <div class="field">
                        <label for="full_name">Full Name</label>
                        <input id="full_name" name="full_name" type="text" placeholder="Enter full name" />
                    </div>

                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" placeholder="Enter email address" />
                    </div>

                    <div class="field">
                        <label for="customer_type">Customer Type</label>
                        <select id="customer_type" name="customer_type">
                            <option value="">Select customer type</option>
                            <option value="Retail">Retail</option>
                            <option value="Institutional">Institutional</option>
                        </select>
                    </div>

                    <div class="field field-actions">
                        <button type="button" class="btn-primary">Create Customer</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</x-layout>
