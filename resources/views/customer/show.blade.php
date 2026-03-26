<x-layout>

    <div class="admin-stack">
        <header class="admin-header">
            <div>
                <h1 class="page-title">{{ $customer->full_name }}</h1>
                <p class="page-subtitle">Customer profile and custody activity overview.</p>
            </div>

            <a href="{{ route('customers') }}" class="btn-ghost">Back to Customers</a>
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
                <div class="kpi-value">{{ $customer->account->holding->sum('value') }}</div>
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
                            <span
                                class="badge {{ $customer->status === 'Active' ? 'badge--success' : 'badge--danger' }}">
                                <span
                                    class="badge-dot {{ $customer->status === 'Active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                {{ $customer->status }}
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
                        @foreach ($customer->account->holding as $row)
                            <tr>
                                <td class="font-semibold">{{ $row->metalType->name }}</td>
                                <td><span class="pill">{{ $row->storage_type }}</span></td>
                                <td>{{ $row->quantity_kg }} kg</td>
                                <td class="num font-semibold">{{ $row->value }}</td>
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
                        @foreach ($customer->account->deposit as $row)
                            @php
                                $isDeposit = $row->type === 'Deposit';
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge {{ $isDeposit ? 'badge--success' : 'badge--danger' }}">
                                        <span
                                            class="badge-dot {{ $isDeposit ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        {{ $row->type }}
                                    </span>
                                </td>
                                <td class="font-semibold">{{ $row->metalType->name }}</td>
                                <td><span class="pill">{{ $row->storage_type }}</span></td>
                                <td>{{ $row->quantity_kg }} kg</td>
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

    <script>
        (() => {
            const openModal = (modal) => {
                if (!modal) return;
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            };

            const closeModal = (modal) => {
                if (!modal) return;
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            const depositModal = document.getElementById('deposit-modal');
            const withdrawalModal = document.getElementById('withdrawal-modal');

            document.querySelector('[data-open-deposit-modal]')?.addEventListener('click', () => openModal(
                depositModal));
            document.querySelector('[data-open-withdrawal-modal]')?.addEventListener('click', () => openModal(
                withdrawalModal));

            document.querySelectorAll('[data-close-deposit-modal]').forEach((el) => {
                el.addEventListener('click', () => closeModal(depositModal));
            });

            document.querySelectorAll('[data-close-withdrawal-modal]').forEach((el) => {
                el.addEventListener('click', () => closeModal(withdrawalModal));
            });

            // ----------------------------
            // Deposit modal logic
            // ----------------------------
            const quantityInput = document.getElementById('deposit-quantity');
            const badge = document.getElementById('deposit-storage-badge');
            const allocatedSection = document.getElementById('allocated-bars-section');

            const barSerialInput = document.getElementById('bar-serial');
            const barWeightInput = document.getElementById('bar-weight');
            const addBarBtn = document.getElementById('add-bar-btn');
            const barsTbody = document.getElementById('bars-tbody');
            const emptyBarsRow = document.getElementById('empty-bars-row');
            const barsInputs = document.getElementById('bars-inputs');
            const totalLabel = document.getElementById('deposit-total-label');

            const storageTypeInput = document.getElementById('deposit-storage-type');
            const enforcedStorageType = (storageTypeInput?.value || 'unallocated').toLowerCase();
            let barIndex = 0;

            const formatKg = (value) => `${Number(value || 0).toFixed(2)} kg`;

            const updateStorageUI = () => {
                const isAllocated = enforcedStorageType === 'allocated';

                if (badge) {
                    badge.textContent = isAllocated ? 'Allocated' : 'Unallocated';
                }

                if (allocatedSection) {
                    allocatedSection.classList.toggle('hidden', !isAllocated);
                }
            };

            const updateTotal = () => {
                if (totalLabel && quantityInput) {
                    totalLabel.textContent = formatKg(quantityInput.value);
                }
            };

            const appendHiddenBarInputs = (index, serial, weight) => {
                if (!barsInputs) return;

                const wrapper = document.createElement('div');
                wrapper.id = `bar-input-${index}`;

                const serialInput = document.createElement('input');
                serialInput.type = 'hidden';
                serialInput.name = `bars[${index}][serial_number]`;
                serialInput.value = serial;

                const weightInput = document.createElement('input');
                weightInput.type = 'hidden';
                weightInput.name = `bars[${index}][weight_kg]`;
                weightInput.value = Number(weight).toFixed(2);

                wrapper.appendChild(serialInput);
                wrapper.appendChild(weightInput);
                barsInputs.appendChild(wrapper);
            };

            const removeHiddenBarInputs = (index) => {
                document.getElementById(`bar-input-${index}`)?.remove();
            };

            if (addBarBtn && barSerialInput && barWeightInput && barsTbody && emptyBarsRow) {
                addBarBtn.addEventListener('click', () => {
                    if (enforcedStorageType !== 'allocated') return;

                    const serial = barSerialInput.value.trim();
                    const weight = Number(barWeightInput.value);

                    if (!serial || Number.isNaN(weight) || weight <= 0) return;

                    const index = barIndex++;
                    emptyBarsRow.classList.add('hidden');

                    const row = document.createElement('tr');
                    row.className = 'bar-row';
                    row.dataset.barIndex = String(index);
                    row.innerHTML = `
                            <td class="font-medium">${serial}</td>
                            <td>${weight.toFixed(2)} kg</td>
                            <td class="num">
                                <button type="button" class="btn-ghost remove-bar-btn">Remove</button>
                            </td>
                        `;
                    barsTbody.appendChild(row);

                    appendHiddenBarInputs(index, serial, weight);

                    row.querySelector('.remove-bar-btn')?.addEventListener('click', (event) => {
                        const currentRow = event.currentTarget.closest('tr');
                        const rowIndex = currentRow?.dataset.barIndex;
                        currentRow?.remove();

                        if (rowIndex) {
                            removeHiddenBarInputs(rowIndex);
                        }

                        if (!barsTbody.querySelector('.bar-row')) {
                            emptyBarsRow.classList.remove('hidden');
                        }
                    });

                    barSerialInput.value = '';
                    barWeightInput.value = '';
                });
            }

            quantityInput?.addEventListener('input', updateTotal);
            updateStorageUI();
            updateTotal();

            // ----------------------------
            // Withdrawal modal logic
            // ----------------------------
            const withdrawalStorage = document.getElementById('withdrawal-storage');
            const withdrawalQty = document.getElementById('withdrawal-quantity');
            const availableBalanceLabel = document.getElementById('available-balance-label');
            const barsSection = document.getElementById('withdrawal-bars-section');
            const selectedTotalLabel = document.getElementById('selected-total-label');
            const warning = document.getElementById('withdrawal-warning');
            const barCheckboxes = Array.from(document.querySelectorAll('.bar-select'));
            const availableBalance = 2.5;

            if (availableBalanceLabel) {
                availableBalanceLabel.textContent = `${availableBalance.toFixed(2)} kg`;
            }

            const selectedBarsTotal = () =>
                barCheckboxes
                .filter((checkbox) => checkbox.checked)
                .reduce((total, checkbox) => total + Number(checkbox.dataset.weight || 0), 0);

            const updateWithdrawalValidation = () => {
                if (!warning || !withdrawalQty) return;
                const quantity = Number(withdrawalQty.value || 0);
                warning.classList.toggle('hidden', !(quantity > availableBalance));
            };

            const updateSelectedTotal = () => {
                if (!selectedTotalLabel) return;
                selectedTotalLabel.textContent = `${selectedBarsTotal().toFixed(2)} kg`;
            };

            const updateWithdrawalStorage = () => {
                if (!barsSection || !withdrawalStorage) return;
                const value = (withdrawalStorage.value || '').toLowerCase();
                barsSection.classList.toggle('hidden', value !== 'allocated');
            };

            withdrawalQty?.addEventListener('input', updateWithdrawalValidation);
            withdrawalStorage?.addEventListener('change', updateWithdrawalStorage);
            barCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', updateSelectedTotal));

            updateWithdrawalStorage();
            updateSelectedTotal();
            updateWithdrawalValidation();
        })();
    </script>

</x-layout>
