import './bootstrap';

import ClipboardJS from 'clipboard';
import Inputmask from 'inputmask';
import './toastify.js';

import './event-handling.js'
import './chat-observer.js'


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
    element.innerHTML = success ? '<i class="fas fa-check"></i> Скопировано!' : '<i class="fas fa-times"></i> Ошибка!';
    element.classList.add(success ? 'copied-success' : 'copied-error');

    setTimeout(() => {
        element.innerHTML = originalHtml;
        element.classList.remove('copied-success', 'copied-error');
    }, 2000);
}

// Инициализация маски для телефона
document.addEventListener('DOMContentLoaded', function () {
    Inputmask({
        mask: '+7 (999) 999-99-99', showMaskOnHover: false, placeholder: '_', greedy: false
    }).mask(document.querySelectorAll('.phone-mask'));
});


function subscribeToUserEvents(userId) {
    Echo.private(`App.Models.User.${userId}`)
        .listen('.new-message-on-chat', event => {
            console.log('Пришло сообщение', event);
            newMessageHandle(event);
        })
        .listen('.message-read', event => {
            messageReadHandle(event);
        });
}

window.subscribeToUserEvents = subscribeToUserEvents;

function addNotificationToList(notification) {
    const listGroup = document.querySelector('.notification-list .list-group');
    if (!listGroup) return;

    const item = document.createElement('div');
    item.className = 'list-group-item unread bg-primary bg-opacity-10 d-flex align-items-start';
    item.id = notification.id ?? `notif-${Date.now()}`;

    const isLink = !!notification.url;
    const textContent = notification.text;

    item.innerHTML = `
        <div class="notification-icon">
            <i class="fas fa-envelope text-primary fa-lg"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold">
                ${isLink
        ? `<a href="${notification.url}" class="link-underline link-underline-opacity-0">${textContent}</a>`
        : `${textContent}`}
            </div>
            <small class="text-muted">только что</small>
        </div>
        <span class="unread-indicator ms-2 text-primary mt-1" title="Непрочитано">
            <i class="fas fa-circle fa-fade" style="font-size: 0.6rem; --fa-animation-duration: 2s; --fa-fade-opacity: 0.6;"></i>
        </span>
    `;

    listGroup.insertBefore(item, listGroup.firstChild);

    let badge = document.getElementById('notification-badge');
    if (badge) {
        const count = parseInt(badge.textContent) || 0;
        badge.textContent = count + 1;
    } else {
        // Если бейдж отсутствует — создать и вставить
        badge = document.createElement('div');
        badge.id = 'notification-badge';
        badge.className = 'position-absolute top-0 start-100 translate-middle badge text-white border ms-1 mt-1';
        badge.style.fontSize = '0.8rem';
        badge.style.padding = '2px 4px';
        badge.innerHTML = `1<div class="visually-hidden">непрочитанных уведомлений</div>`;

        const bellIcon = document.querySelector('.nav-link .fa-bell');
        if (bellIcon && bellIcon.parentElement) {
            bellIcon.parentElement.appendChild(badge);
        }
    }
}

function subscribeToUserNotifications(userId) {
    Echo.private(`App.Models.User.${userId}`)
        .notification((notification) => {
            switch (notification.type) {
                case 'lesson.started':
                    showInfoToast(notification.text, {
                        url: notification.url, withSound: true,
                    });
                    addNotificationToList(notification);
                    break;
                default:
                    console.warn('Неизвестный тип уведомления', notification)
            }
        })
}

window.subscribeToUserNotifications = subscribeToUserNotifications;
