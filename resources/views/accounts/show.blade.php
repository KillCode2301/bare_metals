<x-layout>
    @php
        $accountId = request()->route('account');

        $accounts = [
            [
                'account' => 'AC-104593',
                'customer' => 'Amara Holdings',
                'created' => '2026-01-08',
                'portfolio' => [
                    ['metal' => 'Gold', 'storage' => 'Allocated', 'balanceKg' => 4.50, 'pricePerKg' => 64000],
                    ['metal' => 'Silver', 'storage' => 'Unallocated', 'balanceKg' => 120.00, 'pricePerKg' => 820],
                    ['metal' => 'Platinum', 'storage' => 'Allocated', 'balanceKg' => 1.20, 'pricePerKg' => 31000],
                ],
            ],
            [
                'account' => 'AC-208114',
                'customer' => 'Nora Bennett',
                'created' => '2026-01-23',
                'portfolio' => [
                    ['metal' => 'Gold', 'storage' => 'Allocated', 'balanceKg' => 1.80, 'pricePerKg' => 64000],
                    ['metal' => 'Silver', 'storage' => 'Allocated', 'balanceKg' => 72.00, 'pricePerKg' => 820],
                    ['metal' => 'Platinum', 'storage' => 'Unallocated', 'balanceKg' => 0.50, 'pricePerKg' => 31000],
                ],
            ],
            [
                'account' => 'AC-880431',
                'customer' => 'Kestrel Capital',
                'created' => '2026-02-01',
                'portfolio' => [
                    ['metal' => 'Gold', 'storage' => 'Allocated', 'balanceKg' => 8.20, 'pricePerKg' => 64000],
                    ['metal' => 'Silver', 'storage' => 'Unallocated', 'balanceKg' => 185.00, 'pricePerKg' => 820],
                    ['metal' => 'Platinum', 'storage' => 'Allocated', 'balanceKg' => 1.90, 'pricePerKg' => 31000],
                ],
            ],
            [
                'account' => 'AC-562201',
                'customer' => 'Mateo Silva',
                'created' => '2026-02-14',
                'portfolio' => [
                    ['metal' => 'Gold', 'storage' => 'Allocated', 'balanceKg' => 1.10, 'pricePerKg' => 64000],
                    ['metal' => 'Silver', 'storage' => 'Allocated', 'balanceKg' => 52.00, 'pricePerKg' => 820],
                    ['metal' => 'Platinum', 'storage' => 'Unallocated', 'balanceKg' => 0.40, 'pricePerKg' => 31000],
                ],
            ],
            [
                'account' => 'AC-776029',
                'customer' => 'Summit Bullion Partners',
                'created' => '2026-03-04',
                'portfolio' => [
                    ['metal' => 'Gold', 'storage' => 'Allocated', 'balanceKg' => 6.10, 'pricePerKg' => 64000],
                    ['metal' => 'Silver', 'storage' => 'Unallocated', 'balanceKg' => 96.00, 'pricePerKg' => 820],
                    ['metal' => 'Platinum', 'storage' => 'Allocated', 'balanceKg' => 1.30, 'pricePerKg' => 31000],
                ],
            ],
        ];

        $account = collect($accounts)->firstWhere('account', $accountId) ?? $accounts[0];
        $money = fn ($n) => '$' . number_format($n, 0);
        $kg = fn ($n) => number_format($n, 2) . ' kg';

        $portfolioRows = collect($account['portfolio'])->map(function ($row) {
            $row['totalValue'] = $row['balanceKg'] * $row['pricePerKg'];
            return $row;
        });
        $totalPortfolio = $portfolioRows->sum('totalValue');
    @endphp

    <div class="admin-stack">
        <header class="admin-header">
            <div>
                <h1 class="page-title">Account {{ $account['account'] }}</h1>
                <p class="page-subtitle">{{ $account['customer'] }} • Created {{ \Carbon\Carbon::parse($account['created'])->format('M d, Y') }}</p>
            </div>

            <a href="{{ route('accounts') }}" class="btn-ghost">Back to Accounts</a>
        </header>

        <section aria-label="Account summary" class="dashboard-kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Customer</div>
                <div class="kpi-value">{{ $account['customer'] }}</div>
                <div class="kpi-meta">Account holder</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Account Number</div>
                <div class="kpi-value">{{ $account['account'] }}</div>
                <div class="kpi-meta">Custody reference</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Total Portfolio Value</div>
                <div class="kpi-value">{{ $money($totalPortfolio) }}</div>
                <div class="kpi-meta">Current estimated valuation</div>
            </div>
        </section>

        <section aria-label="Portfolio Breakdown" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Portfolio Breakdown</div>
                    <div class="panel-subtitle">Metal balances by storage type and market value.</div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" class="btn-primary" data-open-deposit-modal>Deposit</button>
                    <button type="button" class="btn-ghost" data-open-withdrawal-modal>Withdraw</button>
                </div>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr class="text-left">
                            <th>Metal</th>
                            <th>Storage Type</th>
                            <th>Balance (kg)</th>
                            <th>Price per kg</th>
                            <th class="num">Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($portfolioRows as $row)
                            <tr>
                                <td class="font-semibold">{{ $row['metal'] }}</td>
                                <td><span class="pill">{{ $row['storage'] }}</span></td>
                                <td>{{ $kg($row['balanceKg']) }}</td>
                                <td>{{ $money($row['pricePerKg']) }}</td>
                                <td class="num font-semibold">{{ $money($row['totalValue']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="font-semibold text-(--muted)">Total Portfolio</td>
                            <td class="num font-semibold">{{ $money($totalPortfolio) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>
    </div>

    <x-deposit-modal :account="$account['account']" :customer="$account['customer']" />
    <x-withdrawal-modal :account="$account['account']" :customer="$account['customer']" />

    <script>
        (() => {
            const openModal = (modal) => {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            };

            const closeModal = (modal) => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            const depositModal = document.getElementById('deposit-modal');
            const withdrawalModal = document.getElementById('withdrawal-modal');

            document.querySelector('[data-open-deposit-modal]')?.addEventListener('click', () => openModal(depositModal));
            document.querySelector('[data-open-withdrawal-modal]')?.addEventListener('click', () => openModal(withdrawalModal));

            document.querySelectorAll('[data-close-deposit-modal]').forEach((el) => el.addEventListener('click', () => closeModal(depositModal)));
            document.querySelectorAll('[data-close-withdrawal-modal]').forEach((el) => el.addEventListener('click', () => closeModal(withdrawalModal)));

            const storageSelect = document.getElementById('deposit-storage');
            const quantityInput = document.getElementById('deposit-quantity');
            const badge = document.getElementById('deposit-storage-badge');
            const allocatedSection = document.getElementById('allocated-bars-section');
            const barSerialInput = document.getElementById('bar-serial');
            const barWeightInput = document.getElementById('bar-weight');
            const addBarBtn = document.getElementById('add-bar-btn');
            const barsTbody = document.getElementById('bars-tbody');
            const emptyBarsRow = document.getElementById('empty-bars-row');
            const totalLabel = document.getElementById('deposit-total-label');

            const formatKg = (value) => `${Number(value || 0).toFixed(2)} kg`;
            const updateStorageUI = () => {
                const isAllocated = storageSelect.value === 'Allocated';
                badge.textContent = storageSelect.value;
                allocatedSection.classList.toggle('hidden', !isAllocated);
            };
            const updateTotal = () => {
                totalLabel.textContent = formatKg(quantityInput.value);
            };

            addBarBtn.addEventListener('click', () => {
                const serial = barSerialInput.value.trim();
                const weight = Number(barWeightInput.value);
                if (!serial || Number.isNaN(weight) || weight <= 0) return;

                emptyBarsRow.classList.add('hidden');
                const row = document.createElement('tr');
                row.className = 'bar-row';
                row.innerHTML = `
                    <td class="font-medium">${serial}</td>
                    <td>${weight.toFixed(2)} kg</td>
                    <td class="num"><button type="button" class="btn-ghost remove-bar-btn">Remove</button></td>
                `;
                barsTbody.appendChild(row);

                row.querySelector('.remove-bar-btn').addEventListener('click', (event) => {
                    event.currentTarget.closest('tr')?.remove();
                    if (!barsTbody.querySelector('.bar-row')) {
                        emptyBarsRow.classList.remove('hidden');
                    }
                });

                barSerialInput.value = '';
                barWeightInput.value = '';
            });

            storageSelect.addEventListener('change', updateStorageUI);
            quantityInput.addEventListener('input', updateTotal);
            updateStorageUI();
            updateTotal();

            const withdrawalStorage = document.getElementById('withdrawal-storage');
            const withdrawalQty = document.getElementById('withdrawal-quantity');
            const availableBalanceLabel = document.getElementById('available-balance-label');
            const barsSection = document.getElementById('withdrawal-bars-section');
            const selectedTotalLabel = document.getElementById('selected-total-label');
            const warning = document.getElementById('withdrawal-warning');
            const barCheckboxes = Array.from(document.querySelectorAll('.bar-select'));
            const availableBalance = 2.5;

            availableBalanceLabel.textContent = `${availableBalance.toFixed(2)} kg`;
            const selectedBarsTotal = () => barCheckboxes
                .filter((checkbox) => checkbox.checked)
                .reduce((total, checkbox) => total + Number(checkbox.dataset.weight || 0), 0);

            const updateWithdrawalValidation = () => {
                const quantity = Number(withdrawalQty.value || 0);
                warning.classList.toggle('hidden', !(quantity > availableBalance));
            };

            const updateSelectedTotal = () => {
                selectedTotalLabel.textContent = `${selectedBarsTotal().toFixed(2)} kg`;
            };

            const updateWithdrawalStorage = () => {
                barsSection.classList.toggle('hidden', withdrawalStorage.value !== 'Allocated');
            };

            withdrawalQty.addEventListener('input', updateWithdrawalValidation);
            withdrawalStorage.addEventListener('change', updateWithdrawalStorage);
            barCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', updateSelectedTotal));

            updateWithdrawalStorage();
            updateSelectedTotal();
            updateWithdrawalValidation();
        })();
    </script>
</x-layout>
