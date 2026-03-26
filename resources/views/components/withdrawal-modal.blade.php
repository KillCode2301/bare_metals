@props(['account', 'customer', 'metalTypes' => collect(), 'enforcedStorageType'])

@php
    $accountId = is_object($account) ? $account->id : null;
    $accountNumber = is_object($account) ? $account->account_number : (string) $account;
    $customerName = is_object($customer) ? $customer->full_name : (string) $customer;
    $normalizedStorageType = strtolower(trim((string) $enforcedStorageType));
    $storageType = $normalizedStorageType === 'allocated' ? 'allocated' : 'unallocated';
    $isAllocatedStorage = $storageType === 'allocated';

    $balancesByMetal = is_object($account) && method_exists($account, 'holding')
        ? $account->holding
            ->where('storage_type', $storageType)
            ->groupBy('metal_type_id')
            ->map(fn($rows) => (float) $rows->sum('balance_kg'))
        : collect();

    $allocatedBars = is_object($account) && method_exists($account, 'allocatedBar')
        ? $account->allocatedBar
            ->where('status', 'allocated')
            ->groupBy('metal_type_id')
        : collect();
@endphp

<div id="withdrawal-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="custody-modal-overlay" data-close-withdrawal-modal></div>

    <div class="custody-modal-frame">
        <div class="custody-modal-card">
            <div class="custody-modal-header">
                <div>
                    <h2 class="custody-modal-title">New Withdrawal</h2>
                    <p class="custody-modal-subtitle">Capture withdrawal details for this account.</p>
                </div>
                <button type="button" class="btn-ghost" data-close-withdrawal-modal>Close</button>
            </div>

            <div class="custody-modal-body">
                <form id="withdrawal-form" action="{{ route('withdrawals.store') }}" method="POST" class="custody-modal-form">
                    @csrf

                    @if ($accountId)
                        <input type="hidden" name="account_id" value="{{ $accountId }}">
                    @endif

                    <section class="custody-modal-section">
                        <h3 class="custody-section-title">Basic Information</h3>
                        <div class="custody-modal-grid">
                            <div class="field">
                                <label for="withdrawal-account-display">Account</label>
                                <input id="withdrawal-account-display" type="text" value="{{ $accountNumber }} - {{ $customerName }}" readonly>
                            </div>
                            <div class="field">
                                <label for="withdrawal-metal">Select Metal Type</label>
                                <select id="withdrawal-metal" name="metal_type_id" required>
                                    @forelse ($metalTypes as $metal)
                                        <option value="{{ $metal->id }}"
                                            data-balance="{{ number_format((float) ($balancesByMetal->get($metal->id) ?? 0), 2, '.', '') }}">
                                            {{ $metal->name }}
                                        </option>
                                    @empty
                                        <option value="" disabled selected>No metal types found</option>
                                    @endforelse
                                </select>
                            </div>

                            <input id="withdrawal-storage-type" type="hidden" name="storage_type" value="{{ $storageType }}">

                            <div class="field">
                                <label for="withdrawal-quantity">Quantity (kg)</label>
                                <input id="withdrawal-quantity" name="quantity_kg" type="number" step="0.01" min="0.01"
                                    placeholder="{{ $isAllocatedStorage ? 'Auto-calculated from selected bars' : '0.00' }}"
                                    {{ $isAllocatedStorage ? 'readonly' : '' }}
                                    required />
                            </div>
                        </div>

                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-(--border) bg-white px-3 py-1 text-xs font-semibold text-(--muted)">
                            <span class="badge-dot bg-emerald-500"></span>
                            <span id="withdrawal-storage-badge">{{ $isAllocatedStorage ? 'Allocated' : 'Unallocated' }}</span>
                        </div>
                    </section>

                    <section class="custody-modal-card-soft">
                        <div class="text-xs font-semibold uppercase tracking-wide text-(--muted)">Balance Info</div>
                        <div class="mt-1 text-sm font-semibold text-(--text)">
                            Available Balance: <span id="available-balance-label">2.50 kg</span>
                        </div>
                    </section>

                    <section id="withdrawal-bars-section"
                        class="custody-modal-accent custody-modal-section {{ $isAllocatedStorage ? '' : 'hidden' }}">
                        <h3 class="custody-section-title">Select Bars to Withdraw</h3>

                        <div class="table-wrap rounded-xl border border-(--border) bg-white">
                            <table class="data-table">
                                <thead>
                                    <tr class="text-left">
                                        <th>Serial Number</th>
                                        <th>Weight (kg)</th>
                                        <th class="num">Select</th>
                                    </tr>
                                </thead>
                                <tbody id="withdrawal-bars-tbody">
                                    @foreach ($metalTypes as $metal)
                                        @foreach ($allocatedBars->get($metal->id, collect()) as $bar)
                                            <tr class="withdrawal-bar-row hidden" data-metal-type-id="{{ $metal->id }}">
                                                <td class="font-medium">{{ $bar->serial_number }}</td>
                                                <td>{{ number_format((float) $bar->weight_kg, 2) }} kg</td>
                                                <td class="num">
                                                    <input type="checkbox" class="bar-select h-4 w-4 accent-emerald-600"
                                                        name="allocated_bar_ids[]" value="{{ $bar->id }}"
                                                        data-metal-type-id="{{ $metal->id }}"
                                                        data-weight="{{ number_format((float) $bar->weight_kg, 2, '.', '') }}" />
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    <tr id="withdrawal-empty-bars-row">
                                        <td colspan="3" class="text-sm text-(--muted)">No allocated bars available for this metal.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-(--border) bg-white px-3 py-1 text-xs font-semibold text-(--muted)">
                            Selected Total: <span id="selected-total-label">0.00 kg</span>
                        </div>
                    </section>

                    <section id="withdrawal-warning" class="hidden rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                        Insufficient balance
                    </section>
                </form>
            </div>

            <div class="custody-modal-footer">
                <button type="button" class="btn-ghost" data-close-withdrawal-modal>Cancel</button>
                <button type="submit" form="withdrawal-form" class="btn-primary">Confirm Withdrawal</button>
            </div>
        </div>
    </div>
</div>
