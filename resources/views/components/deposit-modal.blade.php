@props(['account', 'customer', 'metalTypes' => collect(), 'enforcedStorageType'])

@php
    $accountId = is_object($account) ? $account->id : null;
    $accountNumber = is_object($account) ? $account->account_number : (string) $account;
    $customerName = is_object($customer) ? $customer->full_name : (string) $customer;
    $normalizedStorageType = strtolower(trim((string) $enforcedStorageType));
    $storageType = $normalizedStorageType === 'allocated' ? 'allocated' : 'unallocated';
    $isAllocatedStorage = $storageType === 'allocated';
@endphp

<div id="deposit-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="custody-modal-overlay" data-close-deposit-modal></div>

    <div class="custody-modal-frame">
        <div class="custody-modal-card">
            <div class="custody-modal-header">
                <div>
                    <h2 class="custody-modal-title">New Deposit</h2>
                    <p class="custody-modal-subtitle">Capture deposit details for this account.</p>
                </div>
                <button type="button" class="btn-ghost" data-close-deposit-modal>Close</button>
            </div>

            <div class="custody-modal-body">
                <form id="deposit-form" action="{{ route('deposits.store') }}" method="POST"
                    class="custody-modal-form">
                    @csrf

                    @if ($accountId)
                        <input type="hidden" name="account_id" value="{{ $accountId }}">
                    @endif

                    <section class="custody-modal-section">
                        <h3 class="custody-section-title">Basic Information</h3>

                        <div class="custody-modal-grid">
                            <div class="field">
                                <label for="deposit-account-display">Account</label>
                                <input id="deposit-account-display" type="text"
                                    value="{{ $account->account_number }}" readonly>
                                <input type="hidden" name="account_id" value="{{ $account->id }}">
                            </div>

                            <div class="field">
                                <label for="deposit-metal">Select Metal Type</label>
                                <select id="deposit-metal" name="metal_type_id" required>
                                    @forelse ($metalTypes as $metal)
                                        <option value="{{ $metal->id }}">{{ $metal->name }}</option>
                                    @empty
                                        <option value="" disabled selected>No metal types found</option>
                                    @endforelse
                                </select>
                            </div>

                            <input id="deposit-storage-type" type="hidden" name="storage_type"
                                value="{{ $storageType }}">

                            <div class="field">
                                <label for="deposit-quantity">Quantity (kg)</label>
                                <input id="deposit-quantity" name="quantity_kg" type="number" step="0.01"
                                    min="0.01" placeholder="0.00" required />
                            </div>
                        </div>

                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-(--border) bg-white px-3 py-1 text-xs font-semibold text-(--muted)">
                            <span class="badge-dot bg-emerald-500"></span>
                            <span id="deposit-storage-badge">{{ $isAllocatedStorage ? 'Allocated' : 'Unallocated' }}</span>
                        </div>
                    </section>

                    @if ($isAllocatedStorage)
                        <section id="allocated-bars-section" class="custody-modal-accent custody-modal-section">
                            <h3 class="custody-section-title">Allocated Bars</h3>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div class="field md:col-span-2">
                                    <label for="bar-serial">Serial Number</label>
                                    <input id="bar-serial" type="text" placeholder="e.g. G-2026-00191" />
                                </div>
                                <div class="field">
                                    <label for="bar-weight">Weight (kg)</label>
                                    <input id="bar-weight" type="number" min="0.01" step="0.01"
                                        placeholder="0.00" />
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="button" class="btn-ghost" id="add-bar-btn">Add Bar</button>
                            </div>

                            <div class="mt-4 table-wrap rounded-xl border border-(--border) bg-white">
                                <table class="data-table">
                                    <thead>
                                        <tr class="text-left">
                                            <th>Serial Number</th>
                                            <th>Weight (kg)</th>
                                            <th class="num">Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bars-tbody">
                                        <tr id="empty-bars-row">
                                            <td colspan="3" class="text-sm text-(--muted)">No bars added yet.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div id="bars-inputs"></div>
                        </section>
                    @endif

                    <section class="custody-modal-card-soft">
                        <div class="text-xs font-semibold uppercase tracking-wide text-(--muted)">Summary</div>
                        <div class="mt-1 text-sm font-semibold text-(--text)">
                            Total Deposit: <span id="deposit-total-label">0.00 kg</span>
                        </div>
                    </section>
                </form>
            </div>

            <div class="custody-modal-footer">
                <button type="button" class="btn-ghost" data-close-deposit-modal>Cancel</button>
                <button type="submit" form="deposit-form" class="btn-primary">Create Deposit</button>
            </div>
        </div>
    </div>
</div>
