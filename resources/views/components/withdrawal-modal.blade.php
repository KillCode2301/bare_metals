@props(['account', 'customer'])

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
                <form action="#" class="custody-modal-form">
                    <section class="custody-modal-section">
                        <h3 class="custody-section-title">Basic Information</h3>
                        <div class="custody-modal-grid">
                            <div class="field">
                                <label for="withdrawal-account">Select Account</label>
                                <select id="withdrawal-account" name="account">
                                    <option>{{ $account }} - {{ $customer }}</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="withdrawal-metal">Select Metal Type</label>
                                <select id="withdrawal-metal" name="metal">
                                    <option>Gold</option>
                                    <option>Silver</option>
                                    <option>Platinum</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="withdrawal-storage">Storage Type</label>
                                <select id="withdrawal-storage" name="storage">
                                    <option>Allocated</option>
                                    <option>Unallocated</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="withdrawal-quantity">Quantity (kg)</label>
                                <input id="withdrawal-quantity" name="quantity" type="number" step="0.01" min="0"
                                    placeholder="0.00" />
                            </div>
                        </div>
                    </section>

                    <section class="custody-modal-card-soft">
                        <div class="text-xs font-semibold uppercase tracking-wide text-(--muted)">Balance Info</div>
                        <div class="mt-1 text-sm font-semibold text-(--text)">
                            Available Balance: <span id="available-balance-label">2.50 kg</span>
                        </div>
                    </section>

                    <section id="withdrawal-bars-section" class="custody-modal-accent custody-modal-section">
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
                                <tbody>
                                    <tr>
                                        <td class="font-medium">G-2026-01918</td>
                                        <td>0.80 kg</td>
                                        <td class="num"><input type="checkbox" class="bar-select h-4 w-4 accent-emerald-600" data-weight="0.8" /></td>
                                    </tr>
                                    <tr>
                                        <td class="font-medium">G-2026-01924</td>
                                        <td>1.10 kg</td>
                                        <td class="num"><input type="checkbox" class="bar-select h-4 w-4 accent-emerald-600" data-weight="1.1" /></td>
                                    </tr>
                                    <tr>
                                        <td class="font-medium">G-2026-01931</td>
                                        <td>0.60 kg</td>
                                        <td class="num"><input type="checkbox" class="bar-select h-4 w-4 accent-emerald-600" data-weight="0.6" /></td>
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
                <button type="button" class="btn-primary">Confirm Withdrawal</button>
            </div>
        </div>
    </div>
</div>
