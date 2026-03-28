import "./bootstrap";
import { initDashboardCharts } from "./dashboard-charts";

// Charts are only initialized after DOM is ready to avoid hydration errors
// Charts only run on the dashboard page.

document.addEventListener("DOMContentLoaded", () => {
    if (document.getElementById("dashboard-charts-root")) {
        initDashboardCharts();
    }
});
