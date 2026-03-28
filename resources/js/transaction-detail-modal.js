const formatKg = (n) => `${Number(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} kg`;

const capitalizeStorage = (s) => {
    if (!s) return "";
    return s.charAt(0).toUpperCase() + s.slice(1).toLowerCase();
};

/**
 * @param {Record<string, unknown>} detail
 */
function populateTransactionDetailModal(root, detail) {
    const titleEl = root.querySelector("#tdm-title");
    const subtitleEl = root.querySelector("#tdm-subtitle");
    const kind = detail.kind === "withdrawal" ? "withdrawal" : "deposit";
    if (titleEl) {
        titleEl.textContent = kind === "deposit" ? "Deposit details" : "Withdrawal details";
    }
    if (subtitleEl) {
        subtitleEl.textContent = String(detail.reference ?? "");
    }

    const setValue = (id, value) => {
        const el = root.querySelector(id);
        if (el && "value" in el) {
            el.value = value == null ? "" : String(value);
        }
    };

    setValue("#tdm-account-name", detail.account_name);
    setValue("#tdm-account-number", detail.account_number);
    setValue("#tdm-customer-type", detail.customer_type);
    setValue("#tdm-metal", detail.metal);
    setValue("#tdm-storage-type", capitalizeStorage(String(detail.storage_type ?? "")));
    setValue("#tdm-quantity", formatKg(detail.quantity_kg));
    setValue("#tdm-occurred-at", detail.occurred_at);

    const barsSection = root.querySelector("#tdm-bars-section");
    const barsWrap = root.querySelector("#tdm-bars-table-wrap");
    const barsBody = root.querySelector("#tdm-bars-body");
    const barsLegacy = root.querySelector("#tdm-bars-legacy");
    const barsHeading = root.querySelector("#tdm-bars-heading");

    const storageType = String(detail.storage_type ?? "").toLowerCase();
    const bars = Array.isArray(detail.bars) ? detail.bars : [];
    const isAllocated = storageType === "allocated";

    if (!barsSection || !barsWrap || !barsBody || !barsLegacy) return;

    barsSection.classList.toggle("hidden", !isAllocated);
    if (!isAllocated) {
        return;
    }

    const isInstitutional = String(detail.customer_type ?? "").toLowerCase() === "institutional";
    // Institutional copy tweak on allocated section heading only.
    if (barsHeading) {
        barsHeading.textContent = isInstitutional
            ? "Allocated bars (institutional custody)"
            : "Allocated bars";
    }

    // Legacy path: some withdrawals may have no bar rows loaded in payload; show fallback copy instead of empty table.
    const hasBars = bars.length > 0;
    const showLegacy = !hasBars && kind === "withdrawal";

    barsWrap.classList.toggle("hidden", !hasBars);
    barsLegacy.classList.toggle("hidden", !showLegacy);

    barsBody.innerHTML = "";
    for (const bar of bars) {
        const tr = document.createElement("tr");
        const statusLabel = String(bar.status ?? "");
        const statusDisplay = statusLabel ? capitalizeStorage(statusLabel) : "—";
        tr.innerHTML = `
            <td class="font-medium">${escapeHtml(String(bar.serial_number ?? ""))}</td>
            <td>${formatKg(bar.weight_kg)}</td>
            <td><span class="pill">${escapeHtml(statusDisplay)}</span></td>
        `;
        barsBody.appendChild(tr);
    }
}

// Escape bar serials before innerHTML to avoid XSS from stored data.
function escapeHtml(s) {
    return s
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
}

export function initTransactionDetailModal() {
    const root = document.getElementById("transaction-detail-modal");
    if (!root) return;

    const close = () => {
        root.classList.add("hidden");
        document.body.style.overflow = "";
    };

    const open = (detail) => {
        populateTransactionDetailModal(root, detail);
        root.classList.remove("hidden");
        document.body.style.overflow = "hidden";
    };

    root.querySelectorAll("[data-close-transaction-detail-modal]").forEach((el) => {
        el.addEventListener("click", close);
    });

    document.querySelectorAll("[data-transaction-detail]").forEach((btn) => {
        btn.addEventListener("click", () => {
            const raw = btn.getAttribute("data-transaction-detail");
            if (!raw) return;
            try {
                const detail = JSON.parse(raw);
                open(detail);
            } catch {
                /* ignore malformed payloads */
            }
        });
    });

    const onKey = (e) => {
        if (e.key === "Escape" && !root.classList.contains("hidden")) {
            close();
        }
    };
    document.addEventListener("keydown", onKey);
}
