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

// Global listener for forms or elements with data-confirm
document.addEventListener('click', function(e) {
    const el = e.target.closest('[data-confirm]');
    if (!el) return;

    e.preventDefault();
    const msg = el.getAttribute('data-confirm') || 'Apakah Anda yakin?';
    const isDanger = el.getAttribute('data-confirm-danger') === 'true' || el.classList.contains('text-red-600') || el.classList.contains('bg-red-600');

    window.sbConfirm({
        title: 'Konfirmasi',
        text: msg,
        isDanger: isDanger,
        confirmButtonText: el.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan'
    }).then((result) => {
        if (result.isConfirmed) {
            if (el.tagName === 'A' && el.href) {
                window.location.href = el.href;
            } else if (el.form) {
                el.form.submit();
            } else {
                el.removeAttribute('data-confirm');
                el.click();
            }
        }
    });
});

