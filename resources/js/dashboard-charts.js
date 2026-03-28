import Chart from "chart.js/auto";

/** Axis/tooltips use USD-style formatting; values come from PHP as currency totals. */
function formatMoney(n) {
    return (
        "$" +
        new Intl.NumberFormat("en-US", { maximumFractionDigits: 0 }).format(n)
    );
}

function formatKg(n) {
    return (
        new Intl.NumberFormat("en-US", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(n) + " kg"
    );
}

/**
 * Reads #dashboard-chart-data (JSON from PHP) and creates Chart.js doughnuts:
 * storage split + allocated/unallocated metal mix. Skips charts when data is empty.
 */

export function initDashboardCharts() {
    const dataEl = document.getElementById("dashboard-chart-data");
    if (!dataEl) {
        return;
    }

    let data;
    try {
        data = JSON.parse(dataEl.textContent);
    } catch {
        return;
    }

    const metalPalette = data.metalColors ?? [];

    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: "bottom",
                labels: {
                    boxWidth: 12,
                    padding: 12,
                },
            },
        },
    };

    const storageCanvas = document.getElementById("chart-storage-split");
    if (storageCanvas) {
        const ss = data.storageSplit;
        const total = (ss.values ?? []).reduce((a, b) => a + b, 0);
        if (total > 0) {
            new Chart(storageCanvas, {
                type: "doughnut",
                data: {
                    labels: ss.labels,
                    datasets: [
                        {
                            data: ss.values,
                            backgroundColor: ss.colors,
                            borderWidth: 0,
                        },
                    ],
                },
                options: {
                    ...baseOptions,
                    plugins: {
                        ...baseOptions.plugins,
                        tooltip: {
                            callbacks: {
                                // Tooltip shows dollar slice, percent of doughnut, and optional kg from parallel kgs array.
                                label(ctx) {
                                    const v = ctx.raw;
                                    const sum = ctx.dataset.data.reduce(
                                        (x, y) => x + y,
                                        0,
                                    );
                                    const pct =
                                        sum > 0
                                            ? ((v / sum) * 100).toFixed(1)
                                            : "0.0";
                                    const kg =
                                        ss.kgs &&
                                        ss.kgs[ctx.dataIndex] !== undefined
                                            ? ss.kgs[ctx.dataIndex]
                                            : null;
                                    const moneyPart = `${ctx.label}: ${formatMoney(v)}`;
                                    return kg != null
                                        ? `${moneyPart} (${pct}%) · ${formatKg(kg)}`
                                        : `${moneyPart} (${pct}%)`;
                                },
                            },
                        },
                    },
                },
            });
        }
    }

    // Renders a single metal mix doughnut chart.

    function mountMetal(canvasId, series) {
        const canvas = document.getElementById(canvasId);
        if (!canvas || !series?.labels?.length) {
            return;
        }
        const values = series.values;
        const colors = series.labels.map(
            (_, i) => metalPalette[i % metalPalette.length],
        );
        new Chart(canvas, {
            type: "doughnut",
            data: {
                labels: series.labels,
                datasets: [
                    {
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 0,
                    },
                ],
            },
            options: {
                ...baseOptions,
                plugins: {
                    ...baseOptions.plugins,
                    tooltip: {
                        callbacks: {
                            label(ctx) {
                                const v = ctx.raw;
                                const sum = ctx.dataset.data.reduce(
                                    (x, y) => x + y,
                                    0,
                                );
                                const pct =
                                    sum > 0
                                        ? ((v / sum) * 100).toFixed(1)
                                        : "0.0";
                                const kg =
                                    series.kgs &&
                                    series.kgs[ctx.dataIndex] !== undefined
                                        ? series.kgs[ctx.dataIndex]
                                        : null;
                                const moneyPart = `${ctx.label}: ${formatMoney(v)}`;
                                return kg != null
                                    ? `${moneyPart} (${pct}%) · ${formatKg(kg)}`
                                    : `${moneyPart} (${pct}%)`;
                            },
                        },
                    },
                },
            },
        });
    }

    mountMetal("chart-allocated-metal", data.allocatedMetal);
    mountMetal("chart-unallocated-metal", data.unallocatedMetal);
}
