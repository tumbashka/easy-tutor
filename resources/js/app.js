import './bootstrap';
import ClipboardJS from 'clipboard';

document.addEventListener('DOMContentLoaded', function () {
    const clipboard = new ClipboardJS('.btn-copy', {
        text: function (trigger) {
            return trigger.previousElementSibling.value;
        }
    });

    clipboard.on('success', function (e) {
        showCopyFeedback(e.trigger, true);
        e.clearSelection();
    });

    clipboard.on('error', function (e) {
        showCopyFeedback(e.trigger, false);
    });
});

function showCopyFeedback(element, success) {
    const originalHtml = element.innerHTML;
    element.innerHTML = success ?
        '<i class="fas fa-check"></i> Скопировано!' :
        '<i class="fas fa-times"></i> Ошибка!';
    element.classList.add(success ? 'copied-success' : 'copied-error');

    setTimeout(() => {
        element.innerHTML = originalHtml;
        element.classList.remove('copied-success', 'copied-error');
    }, 2000);
}


import Inputmask from 'inputmask';

// Инициализация маски для телефона
document.addEventListener('DOMContentLoaded', function() {
    Inputmask({
        mask: '+7 (999) 999-99-99',
        showMaskOnHover: false,
        placeholder: '_',
        greedy: false
    }).mask(document.querySelectorAll('.phone-mask'));
});
