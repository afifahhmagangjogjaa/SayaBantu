import './bootstrap';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Swal = Swal;

// SayaBantu Modern Alert & Toast System
const Toast = Swal.mixin({
    toast: true,
    position: 'top',
    showConfirmButton: false,
    timer: 2400,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
    customClass: {
        popup: 'rounded-2xl shadow-xl border border-gray-100 px-4 py-3 bg-white/95 backdrop-blur-md font-sans text-xs font-semibold text-gray-800 flex items-center gap-2.5',
        title: 'text-xs font-bold text-gray-900',
    }
});

window.sbToast = function(title, icon = 'success') {
    return Toast.fire({
        icon: icon,
        title: title
    });
};

// Override native Google Chrome window.alert
window.alert = function (message) {
    if (!message) return;
    const strMsg = String(message);

    // If it's a copy or success message, display sleek toast notification
    if (/disalin|tersalin|copied|copy|berhasil/i.test(strMsg) && strMsg.length < 100) {
        return window.sbToast(strMsg, 'success');
    }

    const isWarning = /peringatan|perhatian|warning|kurang|harus|belum/i.test(strMsg);
    const isError = /gagal|error|salah|batal|rusak|ditolak/i.test(strMsg);

    let icon = 'info';
    let iconColor = '#0066FF';
    let confirmBtnClass = 'bg-[#0066FF] hover:bg-[#0052cc] text-white';

    if (isError) {
        icon = 'error';
        iconColor = '#EF4444';
        confirmBtnClass = 'bg-red-600 hover:bg-red-700 text-white';
    } else if (isWarning) {
        icon = 'warning';
        iconColor = '#F59E0B';
        confirmBtnClass = 'bg-amber-600 hover:bg-amber-700 text-white';
    }

    return Swal.fire({
        text: strMsg,
        icon: icon,
        iconColor: iconColor,
        confirmButtonText: 'Oke, Mengerti',
        buttonsStyling: false,
        customClass: {
            popup: 'rounded-3xl shadow-2xl p-6 border border-gray-100 font-sans max-w-sm',
            confirmButton: `w-full py-3 px-5 rounded-xl font-bold text-xs ${confirmBtnClass} shadow-md transition active:scale-95 cursor-pointer mt-3`,
            htmlContainer: 'text-xs text-gray-600 mt-2 leading-relaxed font-medium',
        }
    });
};

// Global helper for SweetAlert confirm dialogs
window.sbConfirm = function({
    title = 'Apakah Anda yakin?',
    text = 'Tindakan ini tidak dapat dibatalkan.',
    icon = 'warning',
    confirmButtonText = 'Ya, Lanjutkan',
    cancelButtonText = 'Batal',
    isDanger = false
} = {}) {
    if (Swal.isVisible()) {
        Swal.close();
    }
    return Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
        buttonsStyling: false,
        customClass: {
            popup: 'rounded-3xl shadow-2xl p-6 border border-gray-100 font-sans max-w-sm',
            title: 'text-base font-extrabold text-gray-900',
            htmlContainer: 'text-xs text-gray-600 mt-1 leading-relaxed font-medium',
            actions: 'flex gap-2.5 w-full mt-4',
            confirmButton: `flex-1 py-3 px-4 rounded-xl font-bold text-xs ${isDanger ? 'bg-red-600 hover:bg-red-700' : 'bg-[#0066FF] hover:bg-[#0052cc]'} text-white shadow-md transition active:scale-95 cursor-pointer`,
            cancelButton: 'flex-1 py-3 px-4 rounded-xl font-semibold text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 transition active:scale-95 cursor-pointer'
        }
    });
};

// Global listener for non-form elements with data-confirm (e.g. <a> links, standalone action buttons)
document.addEventListener('click', function(e) {
    const el = e.target.closest('[data-confirm]');
    if (!el) return;

    // If the element is a FORM, or inside a FORM with data-confirm, or is a form submit button:
    // Do NOT intercept here. Let the 'submit' event handler below handle it cleanly so that
    // forms are reliably submitted without duplicate alerts or missing submissions.
    if (el.tagName === 'FORM' || el.closest('form[data-confirm]') || (el.form && (el.type === 'submit' || !el.type))) {
        return;
    }

    e.preventDefault();
    const msg = el.getAttribute('data-confirm') || 'Apakah Anda yakin?';
    const isDangerAttr = el.getAttribute('data-confirm-danger');
    const isDanger = isDangerAttr === 'true' || 
        (isDangerAttr !== 'false' && (el.classList.contains('text-red-600') || el.classList.contains('bg-red-600') || el.classList.contains('bg-rose-600') || /hapus|delete|sanksi|nonaktif|keluar/i.test(msg)));

    window.sbConfirm({
        title: 'Konfirmasi',
        text: msg,
        isDanger: isDanger,
        confirmButtonText: el.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan'
    }).then((result) => {
        if (result.isConfirmed) {
            if (el.tagName === 'A' && el.href) {
                window.location.href = el.href;
            } else if (typeof el.onclick === 'function') {
                el.onclick();
            } else {
                const origConfirm = el.getAttribute('data-confirm');
                el.removeAttribute('data-confirm');
                el.click();
                setTimeout(() => {
                    el.setAttribute('data-confirm', origConfirm);
                }, 100);
            }
        }
    });
});

// Global listener for forms with data-confirm or submitted by buttons with data-confirm
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (form.__sbConfirmed) {
        delete form.__sbConfirmed;
        return;
    }

    const submitter = e.submitter || document.activeElement;
    const confirmMsg = form.getAttribute('data-confirm') || submitter?.getAttribute('data-confirm');
    if (confirmMsg) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const dangerAttr = form.getAttribute('data-confirm-danger') || submitter?.getAttribute('data-confirm-danger');
        let isDanger = false;
        if (dangerAttr === 'true') {
            isDanger = true;
        } else if (dangerAttr === 'false') {
            isDanger = false;
        } else {
            isDanger = form.querySelector('.bg-red-600, .text-red-600, .bg-rose-600') !== null ||
                submitter?.classList.contains('bg-red-600') ||
                submitter?.classList.contains('text-red-600') ||
                /hapus|delete|sanksi|nonaktif|keluar/i.test(confirmMsg);
        }

        const confirmBtn = form.getAttribute('data-confirm-btn') || submitter?.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';

        window.sbConfirm({
            title: 'Konfirmasi',
            text: confirmMsg,
            isDanger: isDanger,
            confirmButtonText: confirmBtn
        }).then((result) => {
            if (result.isConfirmed) {
                form.__sbConfirmed = true;
                if (submitter && submitter.name) {
                    let hidden = form.querySelector(`input[type="hidden"][name="${submitter.name}"]`);
                    if (!hidden) {
                        hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = submitter.name;
                        form.appendChild(hidden);
                    }
                    hidden.value = submitter.value;
                }
                form.submit();
            }
        });
    }
}, true);

// Otomatis ubah onsubmit/onclick confirm bawaan browser menjadi modal modern SayaBantu
function convertLegacyBrowserAlerts() {
    document.querySelectorAll('form[onsubmit*="confirm("]').forEach(function(form) {
        const onsubmitStr = form.getAttribute('onsubmit') || '';
        const match = onsubmitStr.match(/confirm\s*\(\s*['"`](.*?)['"`]\s*\)/);
        if (match && match[1]) {
            form.removeAttribute('onsubmit');
            form.setAttribute('data-confirm', match[1]);
            if (/hapus|delete|sanksi|nonaktif|keluar|ekstrem/i.test(match[1])) {
                form.setAttribute('data-confirm-danger', 'true');
            }
        }
    });

    document.querySelectorAll('[onclick*="confirm("]').forEach(function(el) {
        const onclickStr = el.getAttribute('onclick') || '';
        const match = onclickStr.match(/confirm\s*\(\s*['"`](.*?)['"`]\s*\)/);
        if (match && match[1]) {
            el.removeAttribute('onclick');
            el.setAttribute('data-confirm', match[1]);
            if (/hapus|delete|sanksi|nonaktif|keluar|ekstrem/i.test(match[1])) {
                el.setAttribute('data-confirm-danger', 'true');
            }
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', convertLegacyBrowserAlerts);
} else {
    convertLegacyBrowserAlerts();
}
document.addEventListener('livewire:navigated', convertLegacyBrowserAlerts);
document.addEventListener('livewire:init', convertLegacyBrowserAlerts);

// Intercept Livewire 3 wire:confirm to use SayaBantu SweetAlert2 dialogs instead of native browser confirm
document.addEventListener('click', function(e) {
    const el = e.target.closest('[wire\\:confirm]');
    if (el && el.__livewire_confirm && !el.__sbConfirmWrapped) {
        el.__sbConfirmWrapped = true;
        el.__livewire_confirm = (action, instead) => {
            const msg = el.getAttribute('wire:confirm') || 'Apakah Anda yakin?';
            const isDanger = /hapus|delete|sanksi|nonaktif|keluar|ekstrem|blokir|peringatan/i.test(msg) ||
                el.classList.contains('bg-red-600') || el.classList.contains('text-red-600') || el.classList.contains('border-red-200');

            window.sbConfirm({
                title: 'Konfirmasi',
                text: msg,
                isDanger: isDanger,
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    action();
                } else {
                    instead();
                }
            });
        };
    }
}, true);


