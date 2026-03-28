/**
 * Deposit / withdrawal modal behavior for the customer show page
 * (paired `#deposit-modal` + `#withdrawal-modal` from Blade components).
 */
export function initCustodyModals() {
    const openModal = (modal) => {
        if (!modal) return;
        modal.classList.remove("hidden");
        document.body.classList.add("overflow-hidden");
    };

    const closeModal = (modal) => {
        if (!modal) return;
        modal.classList.add("hidden");
        document.body.classList.remove("overflow-hidden");
    };

    const depositModal = document.getElementById("deposit-modal");
    const withdrawalModal = document.getElementById("withdrawal-modal");

    document.querySelector("[data-open-deposit-modal]")?.addEventListener("click", () => openModal(depositModal));
    document.querySelector("[data-open-withdrawal-modal]")?.addEventListener("click", () => openModal(withdrawalModal));

    document.querySelectorAll("[data-close-deposit-modal]").forEach((el) => {
        el.addEventListener("click", () => closeModal(depositModal));
    });

    document.querySelectorAll("[data-close-withdrawal-modal]").forEach((el) => {
        el.addEventListener("click", () => closeModal(withdrawalModal));
    });

    const depositForm = document.getElementById("deposit-form");
    const depositMetal = document.getElementById("deposit-metal");
    const depositSubmitBtn = document.getElementById("deposit-submit-btn");

    // Deposit modal logic
    const quantityInput = document.getElementById("deposit-quantity");
    const badge = document.getElementById("deposit-storage-badge");
    const allocatedSection = document.getElementById("allocated-bars-section");

    const barSerialInput = document.getElementById("bar-serial");
    const barWeightInput = document.getElementById("bar-weight");
    const addBarBtn = document.getElementById("add-bar-btn");
    const barsTbody = document.getElementById("bars-tbody");
    const emptyBarsRow = document.getElementById("empty-bars-row");
    const barsInputs = document.getElementById("bars-inputs");
    const totalLabel = document.getElementById("deposit-total-label");

    const storageTypeInput = document.getElementById("deposit-storage-type");
    // Storage type is fixed per page context from a hidden field set by Blade (not toggled live in UI).
    const enforcedStorageType = (storageTypeInput?.value || "unallocated").toLowerCase();
    const isDepositAllocated = enforcedStorageType === "allocated";
    let barIndex = 0;

    const formatKg = (value) => `${Number(value || 0).toFixed(2)} kg`;

    const isDepositValid = () => {
        if (!depositMetal?.value) return false;
        const q = Number(quantityInput?.value || 0);
        if (!Number.isFinite(q) || q < 0.01) return false;
        if (depositForm && typeof depositForm.checkValidity === "function" && !depositForm.checkValidity()) {
            return false;
        }
        return true;
    };

    const refreshDepositSubmit = () => {
        if (!depositSubmitBtn) return;
        depositSubmitBtn.disabled = !isDepositValid();
    };

    const updateStorageUI = () => {
        if (badge) {
            badge.textContent = isDepositAllocated ? "Allocated" : "Unallocated";
        }
        if (allocatedSection) {
            allocatedSection.classList.toggle("hidden", !isDepositAllocated);
        }
    };

    // For allocated deposits, quantity is derived from bar weights; keeps POST body aligned with server rules.
    const syncDepositQuantityFromBars = () => {
        if (!quantityInput || !isDepositAllocated || !barsInputs) return;

        const allWeightInputs = barsInputs.querySelectorAll('input[name*="[weight_kg]"]');
        const total = Array.from(allWeightInputs).reduce((sum, el) => sum + Number(el.value || 0), 0);

        quantityInput.value = total > 0 ? total.toFixed(2) : "";
        if (totalLabel) {
            totalLabel.textContent = total > 0 ? formatKg(total) : "0.00 kg";
        }
        refreshDepositSubmit();
    };

    const updateTotal = () => {
        if (totalLabel && quantityInput && !isDepositAllocated) {
            totalLabel.textContent = formatKg(quantityInput.value);
        }
        refreshDepositSubmit();
    };

    // Mirror visible table rows into hidden inputs named bars[i][…] for normal form POST to Laravel.
    const appendHiddenBarInputs = (index, serial, weight) => {
        if (!barsInputs) return;

        const wrapper = document.createElement("div");
        wrapper.id = `bar-input-${index}`;

        const serialInput = document.createElement("input");
        serialInput.type = "hidden";
        serialInput.name = `bars[${index}][serial_number]`;
        serialInput.value = serial;

        const weightInput = document.createElement("input");
        weightInput.type = "hidden";
        weightInput.name = `bars[${index}][weight_kg]`;
        weightInput.value = Number(weight).toFixed(2);

        wrapper.appendChild(serialInput);
        wrapper.appendChild(weightInput);
        barsInputs.appendChild(wrapper);
    };

    const removeHiddenBarInputs = (index) => {
        document.getElementById(`bar-input-${index}`)?.remove();
    };

    // Server is source of truth for readonly quantity; user only edits bar list.
    if (isDepositAllocated && quantityInput) {
        quantityInput.readOnly = true;
        quantityInput.placeholder = "Auto-calculated from bars";
    }

    if (addBarBtn && barSerialInput && barWeightInput && barsTbody && emptyBarsRow) {
        addBarBtn.addEventListener("click", () => {
            if (!isDepositAllocated) return;

            const serial = barSerialInput.value.trim();
            const weight = Number(barWeightInput.value);

            if (!serial || Number.isNaN(weight) || weight <= 0) return;

            const index = barIndex++;
            emptyBarsRow.classList.add("hidden");

            const row = document.createElement("tr");
            row.className = "bar-row";
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
            syncDepositQuantityFromBars();

            row.querySelector(".remove-bar-btn")?.addEventListener("click", (event) => {
                const currentRow = event.currentTarget.closest("tr");
                const rowIndex = currentRow?.dataset.barIndex;
                currentRow?.remove();

                if (rowIndex) {
                    removeHiddenBarInputs(rowIndex);
                }

                if (!barsTbody.querySelector(".bar-row")) {
                    emptyBarsRow.classList.remove("hidden");
                }

                syncDepositQuantityFromBars();
            });

            barSerialInput.value = "";
            barWeightInput.value = "";
        });
    }

    if (!isDepositAllocated) {
        quantityInput?.addEventListener("input", updateTotal);
    }

    depositMetal?.addEventListener("change", refreshDepositSubmit);

    updateStorageUI();
    updateTotal();

    const withdrawalForm = document.getElementById("withdrawal-form");
    const withdrawalSubmitBtn = document.getElementById("withdrawal-submit-btn");

    // Withdrawal modal logic
    const withdrawalStorageType = document.getElementById("withdrawal-storage-type");
    const withdrawalStorageBadge = document.getElementById("withdrawal-storage-badge");
    const withdrawalMetal = document.getElementById("withdrawal-metal");
    const withdrawalQty = document.getElementById("withdrawal-quantity");
    const availableBalanceLabel = document.getElementById("available-balance-label");
    const barsSection = document.getElementById("withdrawal-bars-section");
    const withdrawalBarsTbody = document.getElementById("withdrawal-bars-tbody");
    const withdrawalEmptyBarsRow = document.getElementById("withdrawal-empty-bars-row");
    const selectedTotalLabel = document.getElementById("selected-total-label");
    const warning = document.getElementById("withdrawal-warning");
    const barCheckboxes = Array.from(document.querySelectorAll(".bar-select"));

    const selectedStorageType = (withdrawalStorageType?.value || "unallocated").toLowerCase();
    const isAllocatedStorage = selectedStorageType === "allocated";

    const currentAvailableBalance = () => {
        const selectedOption = withdrawalMetal?.selectedOptions?.[0];
        const value = Number(selectedOption?.dataset?.balance || 0);
        return Number.isFinite(value) ? value : 0;
    };

    const selectedBarsTotal = () =>
        barCheckboxes
            .filter((checkbox) => !checkbox.disabled && checkbox.checked)
            .reduce((total, checkbox) => total + Number(checkbox.dataset.weight || 0), 0);

    const resetBarSelection = () => {
        barCheckboxes.forEach((checkbox) => {
            checkbox.checked = false;
            checkbox.disabled = true;
        });
    };

    const updateAvailableBalance = () => {
        if (!availableBalanceLabel) return;
        availableBalanceLabel.textContent = `${currentAvailableBalance().toFixed(2)} kg`;
    };

    // Client-side mirror of server rules: balance cap, allocated bar total within 0.01 kg.
    const isWithdrawalValid = () => {
        if (!withdrawalMetal?.value) return false;
        const quantity = Number(withdrawalQty?.value || 0);
        if (!Number.isFinite(quantity) || quantity < 0.01) return false;
        if (quantity > currentAvailableBalance()) return false;
        if (isAllocatedStorage) {
            if (Math.abs(selectedBarsTotal() - quantity) > 0.01) return false;
        }
        if (withdrawalForm && typeof withdrawalForm.checkValidity === "function" && !withdrawalForm.checkValidity()) {
            return false;
        }
        return true;
    };

    const refreshWithdrawalSubmit = () => {
        if (!withdrawalSubmitBtn) return;
        withdrawalSubmitBtn.disabled = !isWithdrawalValid();
    };

    const updateWithdrawalValidation = () => {
        if (warning && withdrawalQty) {
            const quantity = Number(withdrawalQty.value || 0);
            warning.classList.toggle("hidden", !(quantity > currentAvailableBalance()));
        }
        refreshWithdrawalSubmit();
    };

    const updateSelectedTotal = () => {
        if (!selectedTotalLabel) return;
        selectedTotalLabel.textContent = `${selectedBarsTotal().toFixed(2)} kg`;
    };

    const updateBarsForMetal = () => {
        if (!barsSection || !withdrawalBarsTbody || !withdrawalMetal || !withdrawalEmptyBarsRow) {
            refreshWithdrawalSubmit();
            return;
        }

        if (!isAllocatedStorage) {
            barsSection.classList.add("hidden");
            resetBarSelection();
            withdrawalEmptyBarsRow.classList.remove("hidden");
            refreshWithdrawalSubmit();
            return;
        }

        barsSection.classList.remove("hidden");
        const metalTypeId = withdrawalMetal.value;
        let visibleRows = 0;

        barCheckboxes.forEach((checkbox) => {
            const row = checkbox.closest(".withdrawal-bar-row");
            if (!row) return;

            const shouldShow = checkbox.dataset.metalTypeId === metalTypeId;
            checkbox.checked = false;
            checkbox.disabled = !shouldShow;
            row.classList.toggle("hidden", !shouldShow);

            if (shouldShow) {
                visibleRows += 1;
            }
        });

        withdrawalEmptyBarsRow.classList.toggle("hidden", visibleRows > 0);
        updateSelectedTotal();
        refreshWithdrawalSubmit();
    };

    if (isAllocatedStorage && withdrawalQty) {
        withdrawalQty.readOnly = true;
        withdrawalQty.value = "";
        withdrawalQty.placeholder = "Auto-calculated from selected bars";
    }

    const syncQuantityWithBars = () => {
        if (!withdrawalQty) return;

        if (isAllocatedStorage) {
            const total = selectedBarsTotal();
            withdrawalQty.value = total > 0 ? total.toFixed(2) : "";
        }

        updateWithdrawalValidation();
    };

    if (!isAllocatedStorage) {
        withdrawalQty?.addEventListener("input", updateWithdrawalValidation);
    }

    withdrawalMetal?.addEventListener("change", () => {
        updateAvailableBalance();
        updateBarsForMetal();
        syncQuantityWithBars();
        updateWithdrawalValidation();
    });

    barCheckboxes.forEach((checkbox) =>
        checkbox.addEventListener("change", () => {
            updateSelectedTotal();
            syncQuantityWithBars();
        }),
    );

    // Last-chance validation before POST; prevents confusing 422 responses when JS could explain earlier.
    withdrawalForm?.addEventListener("submit", (event) => {
        const quantity = Number(withdrawalQty?.value || 0);
        const availableBalance = currentAvailableBalance();

        if (quantity <= 0) {
            event.preventDefault();
            if (warning) {
                warning.textContent = isAllocatedStorage
                    ? "Please select at least one bar to withdraw."
                    : "Please enter a quantity greater than 0.";
                warning.classList.remove("hidden");
            }
            return;
        }

        if (quantity > availableBalance) {
            event.preventDefault();
            if (warning) {
                warning.textContent = "Insufficient balance for this withdrawal.";
                warning.classList.remove("hidden");
            }
            return;
        }

        if (isAllocatedStorage) {
            const selectedBarsWeight = selectedBarsTotal();
            if (Math.abs(selectedBarsWeight - quantity) > 0.01) {
                event.preventDefault();
                if (warning) {
                    warning.textContent = "Selected bar total does not match quantity.";
                    warning.classList.remove("hidden");
                }
            }
        }
    });

    if (withdrawalStorageBadge) {
        withdrawalStorageBadge.textContent = isAllocatedStorage ? "Allocated" : "Unallocated";
    }

    updateAvailableBalance();
    updateBarsForMetal();
    updateSelectedTotal();
    updateWithdrawalValidation();
}
