import Swal from 'sweetalert2';

/**
 * Marko Barbershop SweetAlert2 Confirmation & Alert Helper
 */

// Function to check if dark mode is active
const isDarkMode = () => {
    return document.documentElement.classList.contains('dark') || localStorage.getItem('theme') === 'dark';
};

/**
 * Show a styled confirmation dialog.
 * Returns a Promise that resolves to true if confirmed, false otherwise.
 *
 * @param {Object} options
 * @param {string} options.title - Dialog title
 * @param {string} options.text - Dialog description
 * @param {string} [options.html] - Optional HTML content
 * @param {string} [options.icon] - 'warning', 'error', 'success', 'info', 'question'
 * @param {string} [options.confirmButtonText] - Text for confirm button
 * @param {string} [options.cancelButtonText] - Text for cancel button
 * @param {boolean} [options.isDanger] - If true, confirm button gets danger/red styling
 * @param {boolean} [options.showCancelButton] - Default true
 * @returns {Promise<boolean>}
 */
export async function confirmAction({
    title = 'Apakah Anda yakin?',
    text = 'Tindakan ini tidak dapat dibatalkan.',
    html = null,
    icon = 'warning',
    confirmButtonText = 'Ya, Lanjutkan',
    cancelButtonText = 'Batal',
    isDanger = false,
    showCancelButton = true,
    ...customOptions
} = {}) {
    const darkMode = isDarkMode();

    const swalOptions = {
        title,
        icon,
        showCancelButton,
        confirmButtonText,
        cancelButtonText,
        reverseButtons: true,
        focusCancel: isDanger,
        buttonsStyling: false,
        background: darkMode ? '#29211a' : '#ffffff',
        color: darkMode ? '#f3f0eb' : '#29211a',
        customClass: {
            popup: 'marko-swal-popup',
            title: 'marko-swal-title',
            htmlContainer: 'marko-swal-html',
            actions: 'marko-swal-actions',
            confirmButton: isDanger ? 'marko-swal-btn-danger' : 'marko-swal-btn-primary',
            cancelButton: 'marko-swal-btn-cancel',
            icon: 'marko-swal-icon'
        },
        ...customOptions
    };

    if (html) {
        swalOptions.html = html;
    } else if (text) {
        swalOptions.text = text;
    }

    const result = await Swal.fire(swalOptions);
    return result.isConfirmed;
}

/**
 * Toast / notification alert helpers
 */
export const notify = {
    success(title, text = '') {
        return Swal.fire({
            icon: 'success',
            title,
            text,
            confirmButtonText: 'OK',
            buttonsStyling: false,
            customClass: {
                popup: 'marko-swal-popup',
                title: 'marko-swal-title',
                confirmButton: 'marko-swal-btn-primary'
            }
        });
    },
    error(title, text = '') {
        return Swal.fire({
            icon: 'error',
            title,
            text,
            confirmButtonText: 'Tutup',
            buttonsStyling: false,
            customClass: {
                popup: 'marko-swal-popup',
                title: 'marko-swal-title',
                confirmButton: 'marko-swal-btn-danger'
            }
        });
    },
    warning(title, text = '') {
        return Swal.fire({
            icon: 'warning',
            title,
            text,
            confirmButtonText: 'Mengerti',
            buttonsStyling: false,
            customClass: {
                popup: 'marko-swal-popup',
                title: 'marko-swal-title',
                confirmButton: 'marko-swal-btn-primary'
            }
        });
    },
    info(title, text = '') {
        return Swal.fire({
            icon: 'info',
            title,
            text,
            confirmButtonText: 'OK',
            buttonsStyling: false,
            customClass: {
                popup: 'marko-swal-popup',
                title: 'marko-swal-title',
                confirmButton: 'marko-swal-btn-primary'
            }
        });
    }
};

/**
 * Initialize declarative data-confirm auto handler
 * Supports:
 * - Forms with `data-confirm="Message..."`
 * - Buttons/links with `data-confirm="Message..."`
 * - Custom data attributes:
 *   - data-confirm-title="Custom Title"
 *   - data-confirm-btn="Custom Confirm Text"
 *   - data-cancel-btn="Custom Cancel Text"
 *   - data-confirm-type="danger|warning|info|success"
 *   - data-confirm-icon="warning|question|error|info"
 */
export function initConfirmHandler() {
    // Intercept form submissions
    document.addEventListener('submit', async (e) => {
        const form = e.target;
        if (!form || !form.hasAttribute('data-confirm')) return;

        // If form has already been verified by SweetAlert, let it submit
        if (form.dataset.confirmed === 'true') {
            form.dataset.confirmed = 'false';
            return;
        }

        e.preventDefault();

        const message = form.getAttribute('data-confirm') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        const title = form.getAttribute('data-confirm-title') || (form.querySelector('[name="_method"][value="DELETE"]') || form.action.includes('destroy') || form.action.includes('cancel') ? 'Konfirmasi Tindakan' : 'Konfirmasi');
        const isDelete = form.querySelector('[name="_method"][value="DELETE"]') !== null || form.getAttribute('data-confirm-type') === 'danger' || message.toLowerCase().includes('hapus') || message.toLowerCase().includes('batal');
        const confirmBtn = form.getAttribute('data-confirm-btn') || (isDelete ? 'Ya, Lanjutkan' : 'Ya, Konfirmasi');
        const cancelBtn = form.getAttribute('data-cancel-btn') || 'Batal';
        const icon = form.getAttribute('data-confirm-icon') || (isDelete ? 'warning' : 'question');

        const confirmed = await confirmAction({
            title,
            text: message,
            icon,
            isDanger: isDelete,
            confirmButtonText: confirmBtn,
            cancelButtonText: cancelBtn
        });

        if (confirmed) {
            form.dataset.confirmed = 'true';
            form.submit();
        }
    }, true);

    // Intercept clickable elements with data-confirm (like <a> links or non-form buttons)
    document.addEventListener('click', async (e) => {
        const trigger = e.target.closest('[data-confirm]:not(form)');
        if (!trigger) return;

        // If trigger is a submit button inside a form with data-confirm, form handler will catch it
        if (trigger.type === 'submit' && trigger.form && trigger.form.hasAttribute('data-confirm')) {
            return;
        }

        e.preventDefault();

        const message = trigger.getAttribute('data-confirm') || 'Apakah Anda yakin?';
        const title = trigger.getAttribute('data-confirm-title') || 'Konfirmasi';
        const isDanger = trigger.getAttribute('data-confirm-type') === 'danger' || message.toLowerCase().includes('hapus') || message.toLowerCase().includes('batal');
        const confirmBtn = trigger.getAttribute('data-confirm-btn') || (isDanger ? 'Ya, Lanjutkan' : 'Ya, Konfirmasi');
        const cancelBtn = trigger.getAttribute('data-cancel-btn') || 'Batal';
        const icon = trigger.getAttribute('data-confirm-icon') || (isDanger ? 'warning' : 'question');

        const confirmed = await confirmAction({
            title,
            text: message,
            icon,
            isDanger,
            confirmButtonText: confirmBtn,
            cancelButtonText: cancelBtn
        });

        if (confirmed) {
            if (trigger.tagName === 'A' && trigger.href) {
                window.location.href = trigger.href;
            } else if (trigger.form) {
                trigger.form.dataset.confirmed = 'true';
                trigger.form.submit();
            }
        }
    });
}

// Assign to window for global access in inline scripts or Blade files
window.Swal = Swal;
window.confirmAction = confirmAction;
window.showConfirm = confirmAction;
window.notify = notify;
