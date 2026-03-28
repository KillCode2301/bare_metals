const DEFAULT_DURATION_MS = 4800;

/**
 * @typedef {'success' | 'error' | 'warning' | 'info'} ToastVariant
 */

/**
 * @param {object} options
 * @param {string} options.message
 * @param {ToastVariant} [options.variant]
 * @param {number} [options.durationMs]
 */
export function showToast({ message, variant = "info", durationMs = DEFAULT_DURATION_MS }) {
    const stack = ensureToastStack();
    if (!stack || !message) return;

    const toast = document.createElement("div");
    toast.className = `toast toast--${variant}`;
    toast.setAttribute("role", "status");
    toast.textContent = message;

    stack.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.add("toast--visible");
    });

    window.setTimeout(() => {
        toast.classList.remove("toast--visible");
        window.setTimeout(() => toast.remove(), 240);
    }, durationMs);
}

function ensureToastStack() {
    return document.getElementById("toast-stack");
}

/**
 * Reads Laravel session flash from `#app-flash-payload` (set in the layout).
 */
export function initFlashToasts() {
    const el = document.getElementById("app-flash-payload");
    if (!el) return;

    let data;
    try {
        data = JSON.parse(el.textContent || "{}");
    } catch {
        return;
    }

    if (data.error) {
        showToast({ message: String(data.error), variant: "error" });
        return;
    }
    if (data.success) {
        showToast({ message: String(data.success), variant: "success" });
        return;
    }
    if (data.warning) {
        showToast({ message: String(data.warning), variant: "warning" });
    }
}
