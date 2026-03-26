@props(['account', 'customer'])

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
                <form action="#" class="custody-modal-form">
                    <section class="custody-modal-section">
                        <h3 class="custody-section-title">Basic Information</h3>
                        <div class="custody-modal-grid">
                            <div class="field">
                                <label for="deposit-account">Select Account</label>
                                <select id="deposit-account" name="account">
                                    <option>{{ $account }} - {{ $customer }}</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="deposit-metal">Select Metal Type</label>
                                <select id="deposit-metal" name="metal">
                                    <option>Gold</option>
                                    <option>Silver</option>
                                    <option>Platinum</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="deposit-storage">Storage Type</label>
                                <select id="deposit-storage" name="storage">
                                    <option>Allocated</option>
                                    <option>Unallocated</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="deposit-quantity">Quantity (kg)</label>
                                <input id="deposit-quantity" name="quantity" type="number" step="0.01" min="0" placeholder="0.00" />
                            </div>
                        </div>

                        <div class="inline-flex items-center gap-2 rounded-full border border-(--border) bg-white px-3 py-1 text-xs font-semibold text-(--muted)">
                            <span class="badge-dot bg-emerald-500"></span>
                            <span id="deposit-storage-badge">Allocated</span>
                        </div>
                    </section>

                    <section id="allocated-bars-section" class="custody-modal-accent custody-modal-section">
                        <h3 class="custody-section-title">Allocated Bars</h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="field md:col-span-2">
                                <label for="bar-serial">Serial Number</label>
                                <input id="bar-serial" type="text" placeholder="e.g. G-2026-00191" />
                            </div>
                            <div class="field">
                                <label for="bar-weight">Weight (kg)</label>
                                <input id="bar-weight" type="number" min="0" step="0.01" placeholder="0.00" />
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
                    </section>

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
                <button type="button" class="btn-primary">Create Deposit</button>
            </div>
        </div>
    </div>
</div>
