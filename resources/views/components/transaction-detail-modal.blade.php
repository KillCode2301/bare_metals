<div id="transaction-detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="custody-modal-overlay" data-close-transaction-detail-modal></div>

    <div class="custody-modal-frame">
        <div class="custody-modal-card">
            <div class="custody-modal-header">
                <div>
                    <h2 id="tdm-title" class="custody-modal-title">Transaction details</h2>
                    <p id="tdm-subtitle" class="custody-modal-subtitle"></p>
                </div>
                <button type="button" class="btn-ghost" data-close-transaction-detail-modal>Close</button>
            </div>

            <div class="custody-modal-body">
                <section class="custody-modal-section">
                    <h3 class="custody-section-title">Account and custody</h3>
                    <div class="custody-modal-grid">
                        <div class="field">
                            <label>Account name</label>
                            <input id="tdm-account-name" type="text" readonly tabindex="-1">
                        </div>
                        <div class="field">
                            <label>Account number</label>
                            <input id="tdm-account-number" type="text" readonly tabindex="-1">
                        </div>
                        <div class="field">
                            <label>Customer type</label>
                            <input id="tdm-customer-type" type="text" readonly tabindex="-1">
                        </div>
                        <div class="field">
                            <label>Metal type</label>
                            <input id="tdm-metal" type="text" readonly tabindex="-1">
                        </div>
                        <div class="field">
                            <label>Storage type</label>
                            <input id="tdm-storage-type" type="text" readonly tabindex="-1">
                        </div>
                        <div class="field">
                            <label>Quantity</label>
                            <input id="tdm-quantity" type="text" readonly tabindex="-1">
                        </div>
                        <div class="field md:col-span-2">
                            <label>Date and time</label>
                            <input id="tdm-occurred-at" type="text" readonly tabindex="-1">
                        </div>
                    </div>
                </section>

                <section id="tdm-bars-section" class="custody-modal-section hidden">
                    <h3 id="tdm-bars-heading" class="custody-section-title">Allocated bars</h3>
                    <div id="tdm-bars-table-wrap" class="table-wrap hidden">
                        <table class="data-table">
                            <thead>
                                <tr class="text-left">
                                    <th>Serial</th>
                                    <th>Weight (kg)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="tdm-bars-body"></tbody>
                        </table>
                    </div>
                    <p id="tdm-bars-legacy" class="hidden text-sm text-muted leading-relaxed">
                        Bar allocation was not recorded for this withdrawal.
                    </p>
                </section>
            </div>

            <div class="custody-modal-footer">
                <button type="button" class="btn-primary" data-close-transaction-detail-modal>Done</button>
            </div>
        </div>
    </div>
</div>
