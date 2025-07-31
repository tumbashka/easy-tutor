import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';

// const toastSound = new Audio('/sounds/notification.mp3');

function calcDuration(text, base = 3000, perChar = 40, max = 10000) {
    return Math.min(max, base + text.length * perChar);
}

// function playSound() {
//     toastSound?.play().catch(() => {});
// }

function showToast({
                       text,
                       type = 'info',
                       duration,
                       gravity = 'top',
                       position = 'right',
                       isHtml = true,
                       withSound = false,
                       url
                   }) {
    const config = getToastConfig(type);

    Toastify({
        text: isHtml ? `<i class="${config.icon} me-2"></i>${text}` : text,
        duration: duration ?? calcDuration(text),
        gravity,
        position,
        stopOnFocus: true,
        close: true,
        escapeMarkup: !isHtml,
        destination: url ?? undefined,
        style: {
            background: config.gradient,
            color: '#fff',
            fontFamily: '"Arial", sans-serif',
            padding: '15px 20px',
            borderRadius: '5px',
        },
    }).showToast();

    // if (withSound) {
    //     playSound();
    // }
}

function getToastConfig(type) {
    switch (type) {
        case 'success':
            return {
                icon: 'fas fa-check-circle',
                gradient: 'linear-gradient(45deg, #a12f4a 0%, #00cc00 20%, #00cc00 80%, #a12f4a 100%)'
            };
        case 'error':
            return {
                icon: 'fas fa-exclamation-circle',
                gradient: 'linear-gradient(45deg, #a12f4a 0%, #f44336 20%, #f44336 80%, #a12f4a 100%)'
            };
        case 'warning':
            return {
                icon: 'fas fa-exclamation-triangle',
                gradient: 'linear-gradient(45deg, #a12f4a 0%, #ff9800 20%, #ff9800 80%, #a12f4a 100%)'
            };
        case 'info':
        default:
            return {
                icon: 'fas fa-info-circle',
                gradient: 'linear-gradient(45deg, #a12f4a 0%, #2196f3 20%, #2196f3 80%, #a12f4a 100%)'
            };
    }
}


function showSuccessToast(text, options = {}) {
    showToast({ text, type: 'success', ...options });
}

function showErrorToast(text, options = {}) {
    showToast({ text, type: 'error', ...options });
}

function showWarningToast(text, options = {}) {
    showToast({ text, type: 'warning', ...options });
}

function showInfoToast(text, options = {}) {
    showToast({ text, type: 'info', ...options });
}

window.showSuccessToast = showSuccessToast;
window.showErrorToast = showErrorToast;
window.showWarningToast = showWarningToast;
window.showInfoToast = showInfoToast;
