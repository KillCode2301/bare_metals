import "./bootstrap";
import { initCustodyModals } from "./custody-modals";
import { initDashboardCharts } from "./dashboard-charts";
import { initTransactionDetailModal } from "./transaction-detail-modal";
import { initFlashToasts } from "./toast";

document.addEventListener("DOMContentLoaded", () => {
    initFlashToasts();

    // Dashboard chart script runs only when the Blade root element is present (other pages skip Chart.js).
    if (document.getElementById("dashboard-charts-root")) {
        initDashboardCharts();
    }
    if (document.getElementById("transaction-detail-modal")) {
        initTransactionDetailModal();
    }
    // Custody modals live on customer show; gate init so other pages do not query missing DOM ids.
    if (document.getElementById("deposit-modal")) {
        initCustodyModals();
    }
});
