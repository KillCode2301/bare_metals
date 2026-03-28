<x-layout>
    <div class="admin-stack">
        <header class="admin-header">
            <div>
                <h1 class="page-title">Metal Types</h1>
                <p class="page-subtitle">Manage the different types of metals that can be stored in the system.</p>
            </div>

            <button type="button" class="btn-primary" data-open-metal-create-modal>
                <span aria-hidden="true">+</span>
                Add Metal Type
            </button>
        </header>

        <section aria-label="Metal types table" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Metal Catalog</div>
                    <div class="panel-subtitle">Codes, display names, and reference price per kilogram.</div>
                </div>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr class="text-left">
                            <th>Name</th>
                            <th>Code</th>
                            <th class="num">Price / kg (USD)</th>
                            <th class="num">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($metalTypes as $metalType)
                            <tr>
                                <td class="font-semibold">{{ $metalType->name }}</td>
                                <td>
                                    <span class="pill">{{ $metalType->code }}</span>
                                </td>
                                <td class="num font-medium">
                                    ${{ number_format((float) $metalType->current_price_per_kg, 2) }}
                                </td>
                                <td class="num">
                                    <div class="inline-flex flex-wrap items-center justify-end gap-2">
                                        <button type="button" class="btn-ghost" data-open-metal-edit-modal
                                            data-metal-type-id="{{ $metalType->id }}"
                                            data-code="{{ e($metalType->code) }}" data-name="{{ e($metalType->name) }}"
                                            data-current-price-per-kg="{{ $metalType->current_price_per_kg }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('metal-types.destroy', $metalType) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ghost text-red-600 hover:text-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <x-metal-create-modal />
    <x-metal-edit-modal />

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

            const createModal = document.getElementById('metal-create-modal');
            const editModal = document.getElementById('metal-edit-modal');
            const editForm = document.getElementById('metal-edit-form');
            const metalTypesBaseUrl = @json(url('metal-types'));

            document.querySelector('[data-open-metal-create-modal]')?.addEventListener('click', () => {
                openModal(createModal);
            });

            document.querySelectorAll('[data-close-metal-create-modal]').forEach((el) => {
                el.addEventListener('click', () => closeModal(createModal));
            });

            document.querySelectorAll('[data-open-metal-edit-modal]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.metalTypeId;
                    if (!id || !editForm) return;
                    editForm.action = `${metalTypesBaseUrl}/${id}`;
                    const codeEl = document.getElementById('metal-edit-code');
                    const nameEl = document.getElementById('metal-edit-name');
                    const priceEl = document.getElementById('metal-edit-price');
                    if (codeEl) codeEl.value = btn.dataset.code ?? '';
                    if (nameEl) nameEl.value = btn.dataset.name ?? '';
                    if (priceEl) priceEl.value = btn.dataset.currentPricePerKg ?? '';
                    openModal(editModal);
                });
            });

            document.querySelectorAll('[data-close-metal-edit-modal]').forEach((el) => {
                el.addEventListener('click', () => closeModal(editModal));
            });
        })();
    </script>
</x-layout>
