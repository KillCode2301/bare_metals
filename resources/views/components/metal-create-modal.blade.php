<div id="metal-create-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="custody-modal-overlay" data-close-metal-create-modal></div>

    <div class="custody-modal-frame">
        <div class="custody-modal-card">
            <div class="custody-modal-header">
                <div>
                    <h2 class="custody-modal-title">Create Metal Type</h2>
                    <p class="custody-modal-subtitle">Add a new metal that can be held in custody accounts.</p>
                </div>
                <button type="button" class="btn-ghost" data-close-metal-create-modal>Close</button>
            </div>

            <div class="custody-modal-body">
                <form id="metal-create-form" action="{{ route('metal-types.store') }}" method="POST"
                    class="custody-modal-form">
                    @csrf

                    <section class="custody-modal-section">
                        <h3 class="custody-section-title">Details</h3>
                        <div class="custody-modal-grid">
                            <div class="field">
                                <label for="metal-create-code">Code</label>
                                <input id="metal-create-code" name="code" type="text" required maxlength="255"
                                    placeholder="e.g. AU" autocomplete="off" />
                            </div>
                            <div class="field">
                                <label for="metal-create-name">Name</label>
                                <input id="metal-create-name" name="name" type="text" required maxlength="255"
                                    placeholder="e.g. Gold" autocomplete="off" />
                            </div>
                            <div class="field">
                                <label for="metal-create-price">Current price per kg (USD)</label>
                                <input id="metal-create-price" name="current_price_per_kg" type="number" step="0.01"
                                    min="0" required placeholder="0.00" />
                            </div>
                        </div>
                    </section>
                </form>
            </div>

            <div class="custody-modal-footer">
                <button type="button" class="btn-ghost" data-close-metal-create-modal>Cancel</button>
                <button type="submit" form="metal-create-form" class="btn-primary">Create Metal Type</button>
            </div>
        </div>
    </div>
</div>
