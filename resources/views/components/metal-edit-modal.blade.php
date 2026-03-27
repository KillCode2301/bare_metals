<div id="metal-edit-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="custody-modal-overlay" data-close-metal-edit-modal></div>

    <div class="custody-modal-frame">
        <div class="custody-modal-card">
            <div class="custody-modal-header">
                <div>
                    <h2 class="custody-modal-title">Edit Metal Type</h2>
                    <p class="custody-modal-subtitle">Update code, name, or spot price for this metal.</p>
                </div>
                <button type="button" class="btn-ghost" data-close-metal-edit-modal>Close</button>
            </div>

            <div class="custody-modal-body">
                <form id="metal-edit-form" action="" method="POST" class="custody-modal-form">
                    @csrf
                    @method('PUT')

                    <section class="custody-modal-section">
                        <h3 class="custody-section-title">Details</h3>
                        <div class="custody-modal-grid">
                            <div class="field">
                                <label for="metal-edit-code">Code</label>
                                <input id="metal-edit-code" name="code" type="text" required maxlength="255"
                                    autocomplete="off" />
                            </div>
                            <div class="field">
                                <label for="metal-edit-name">Name</label>
                                <input id="metal-edit-name" name="name" type="text" required maxlength="255"
                                    autocomplete="off" />
                            </div>
                            <div class="field">
                                <label for="metal-edit-price">Current price per kg (USD)</label>
                                <input id="metal-edit-price" name="current_price_per_kg" type="number" step="0.01"
                                    min="0" required />
                            </div>
                        </div>
                    </section>
                </form>
            </div>

            <div class="custody-modal-footer">
                <button type="button" class="btn-ghost" data-close-metal-edit-modal>Cancel</button>
                <button type="submit" form="metal-edit-form" class="btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>
