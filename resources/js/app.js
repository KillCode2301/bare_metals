import "./bootstrap";
import { initCustodyModals } from "./custody-modals";
import { initDashboardCharts } from "./dashboard-charts";
import { initTransactionDetailModal } from "./transaction-detail-modal";
import { initFlashToasts } from "./toast";

// Charts are only initialized after DOM is ready to avoid hydration errors
// Charts only run on the dashboard page.

document.addEventListener("DOMContentLoaded", () => {
    initFlashToasts();

    if (document.getElementById("dashboard-charts-root")) {
        initDashboardCharts();
    }
    if (document.getElementById("transaction-detail-modal")) {
        initTransactionDetailModal();
    }
    if (document.getElementById("deposit-modal")) {
        initCustodyModals();
    }
});
