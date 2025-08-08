// Обновление счетчика непрочитанных чатов
async function updateUnreadCount(event) {
    const chatId = event.chat.id;
    const list = event.user_chat.accepted
        ? document.querySelector('#my-chats ul.list-group')
        : document.querySelector('#new-chats ul.list-group');

    if (!list) {
        console.log('Список чатов не найден');
        return;
    }

    // Проверяем, есть ли в чате уже непрочитанные сообщения
    const chatElement = list.querySelector(`a[data-chat-id="${chatId}"]`);
    const hasUnread = chatElement && chatElement.querySelector('i.fas.fa-circle');

    // Если в чате уже есть непрочитанные сообщения, не обновляем счетчик
    if (hasUnread) {
        console.log(`Чат ${chatId} уже имеет непрочитанные сообщения, счетчик не обновляется`);
        return;
    }

    try {
        // Запрашиваем актуальное количество непрочитанных чатов
        const response = await fetch('/chat/count-unread', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });

        if (!response.ok) throw new Error(`Ошибка получения счетчика: ${response.statusText}`);

        const data = await response.json();
        const count = data.count;

        // Находим или создаем элемент счетчика
        let unreadBadge = document.getElementById('unread-messages-badge');
        const badgeContainer = document.getElementById('unread-messages-container');

        if (!unreadBadge && badgeContainer && count > 0) {
            unreadBadge = document.createElement('div');
            unreadBadge.id = 'unread-messages-badge';
            unreadBadge.className = 'position-absolute top-0 start-100 translate-middle badge text-white border ms-1 mt-1';
            unreadBadge.style.cssText = 'font-size: 0.8rem; padding: 2px 4px;';
            unreadBadge.innerHTML = `
                ${count}
                <div class="visually-hidden">непрочитанных сообщений</div>
            `;
            badgeContainer.appendChild(unreadBadge);
            console.log('Создан новый badge с count:', count);
        } else if (unreadBadge) {
            unreadBadge.innerHTML = `
                ${count}
                <div class="visually-hidden">непрочитанных сообщений</div>
            `;
            console.log('Обновлен badge с count:', count);
        } else if (count === 0 && unreadBadge) {
            unreadBadge.remove();
            console.log('Badge удален, так как count = 0');
        }
    } catch (err) {
        console.error('Ошибка в updateUnreadCount:', err);
    }
}

// Обновление списка чатов
function updateChatList(event) {
    const chatId = event.chat.id;
    const messageId = event.message.id;
    const isAccepted = event.user_chat.accepted;
    const list = isAccepted
        ? document.querySelector('#my-chats ul.list-group')
        : document.querySelector('#new-chats ul.list-group');

    if (!list) {
        console.log('Список чатов не найден');
        return;
    }

    // Проверяем, существует ли чат в списке
    let chatElement = list.querySelector(`a[data-chat-id="${chatId}"]`);

    if (chatElement) {
        // Обновляем последнее сообщение и ID
        chatElement.dataset.lastMessageId = messageId;
        const lastMessageText = chatElement.querySelector('.small');
        lastMessageText.innerHTML = `
            <span class="me-2">
                <i class="fas fa-circle text-primary fa-2xs"></i>
            </span>
            ${event.message.text}
        `;
        // Перемещаем чат в начало списка, если он не первый
        if (chatElement.parentElement !== list.firstElementChild) {
            list.prepend(chatElement.parentElement);
            console.log(`Чат ${chatId} перемещен в начало`);
        } else {
            console.log(`Чат ${chatId} уже в начале списка`);
        }
    } else {
        // Создаем новый элемент чата
        const newChatElement = document.createElement('li');
        newChatElement.className = 'list-group-item border-light-subtle d-flex align-items-center';
        newChatElement.innerHTML = `
            <a href="${event.chat.url}" data-chat-id="${chatId}" data-last-message-id="${messageId}" class="text-decoration-none d-flex align-items-center flex-grow-1 text-dark chat-link">
                <img src="${event.chat.avatar_url || '/default-avatar.png'}" class="rounded-circle me-2" width="40" height="40" alt="avatar">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <strong>${event.chat.name}</strong>
                        <small>${new Date(event.message.created_at).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' })}</small>
                    </div>
                    <div class="small">
                        <span class="me-2">
                            <i class="fas fa-circle text-primary fa-2xs"></i>
                        </span>
                        ${event.message.text}
                    </div>
                </div>
            </a>
            <div class="dropdown ms-2">
                <button class="btn btn-sm btn-link text-dark" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    ${isAccepted ? `
                        <li><a class="dropdown-item" href="#">Архивировать</a></li>
                        <li><a class="dropdown-item" href="#">Удалить</a></li>
                        <li><a class="dropdown-item" href="#">Отметить как непрочитанное</a></li>
                    ` : `
                        <li><a class="dropdown-item" href="${event.chat.url}/accept">Принять</a></li>
                        <li><a class="dropdown-item" href="#">Отклонить</a></li>
                        <li><a class="dropdown-item" href="#">Заблокировать</a></li>
                    `}
                </ul>
            </div>
        `;
        // Добавляем чат в начало списка
        list.prepend(newChatElement);
        console.log(`Чат ${chatId} добавлен в список`);
    }
}

// Обновление статуса сообщения в списке чатов
async function updateChatListReadStatus(chatId, messageId) {
    console.log(`Обновление статуса прочтения для chatId: ${chatId}, messageId: ${messageId}`);
    const chatLists = [
        document.querySelector('#my-chats ul.list-group'),
        document.querySelector('#new-chats ul.list-group')
    ];

    let chatElement = null;
    let listFound = null;

    chatLists.forEach(list => {
        if (list && !chatElement) {
            chatElement = list.querySelector(`a[data-chat-id="${chatId}"]`);
            if (chatElement) listFound = list;
        }
    });

    if (!listFound || !chatElement) {
        console.log(`Чат ${chatId} не найден в списках для обновления статуса прочтения`);
        return;
    }

    try {
        // Первый запрос: проверка непрочитанных сообщений в чате
        const response = await fetch(`/chat/${chatId}/unread-count`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });
        console.log('Ответ от /chat/{chat}/unread-count:', response);
        if (!response.ok) throw new Error(`Ошибка получения количества непрочитанных сообщений: ${response.statusText}`);
        const data = await response.json();
        console.log(`Количество непрочитанных сообщений в чате ${chatId}: ${data.unread_count}`);

        // Удаляем индикатор только если нет непрочитанных сообщений
        if (data.unread_count === 0) {
            const unreadIndicator = chatElement.querySelector('i.fas.fa-circle');
            if (unreadIndicator) {
                unreadIndicator.parentElement.remove();
                console.log(`Индикатор непрочитанного сообщения удален из чата ${chatId}, так как нет непрочитанных сообщений`);
            }
        } else {
            console.log(`Чат ${chatId} имеет ${data.unread_count} непрочитанных сообщений, индикатор не удален`);
        }

        // Второй запрос: обновление общего счетчика непрочитанных чатов
        const countResponse = await fetch('/chat/count-unread', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });

        if (!countResponse.ok) throw new Error(`Ошибка получения счетчика: ${countResponse.statusText}`);
        const countData = await countResponse.json(); // Исправлено: countResponse вместо response
        console.log('Общий счетчик непрочитанных чатов:', countData.count);

        const unreadBadge = document.getElementById('unread-messages-badge');
        const badgeContainer = document.getElementById('unread-messages-container');

        if (!unreadBadge && badgeContainer && countData.count > 0) {
            const newBadge = document.createElement('div');
            newBadge.id = 'unread-messages-badge';
            newBadge.className = 'position-absolute top-0 start-100 translate-middle badge text-white border ms-1 mt-1';
            newBadge.style.cssText = 'font-size: 0.8rem; padding: 2px 4px;';
            newBadge.innerHTML = `
                ${countData.count}
                <div class="visually-hidden">непрочитанных сообщений</div>
            `;
            badgeContainer.appendChild(newBadge);
            console.log('Создан новый badge с count:', countData.count);
        } else if (unreadBadge) {
            if (countData.count > 0) {
                unreadBadge.innerHTML = `
                    ${countData.count}
                    <div class="visually-hidden">непрочитанных сообщений</div>
                `;
                console.log('Обновлен badge с count:', countData.count);
            } else {
                unreadBadge.remove();
                console.log('Badge удален, так как count = 0');
            }
        }
    } catch (err) {
        console.error('Ошибка при обновлении статуса прочтения:', err);
    }
}

// Обновление открытого чата
function updateOpenChat(event) {
    const chatBody = document.getElementById('chat-body');
    if (!chatBody) {
        console.log('chat-body не найден, страница чата не открыта');
        return;
    }

    const selectedChatId = chatBody.dataset.chatId || null;
    console.log('updateOpenChat called, selectedChatId:', selectedChatId, 'event.chat.id:', event.chat.id);

    if (selectedChatId && parseInt(selectedChatId) === event.chat.id) {
        const sentinelBottom = document.getElementById('sentinel-bottom');
        console.log('chatBody:', chatBody, 'sentinelBottom:', sentinelBottom);

        const newMessageElement = document.createElement('div');
        newMessageElement.id = `message${event.message.id}`;
        newMessageElement.className = `message-wrapper received unread`;
        newMessageElement.innerHTML = `
            <div class="message-bubble received">
                <div class="fw-bold small">${event.user.name}</div>
                <div class="my-1">${event.message.text}</div>
                <div class="message-meta">
                    ${new Date(event.message.created_at).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' })}
                </div>
            </div>
            <div class="ms-3 d-flex align-items-center unread-message">
                <i class="fas fa-circle text-primary fa-2xs"></i>
            </div>
        `;
        chatBody.insertBefore(newMessageElement, sentinelBottom);
        chatBody.scrollTop = chatBody.scrollHeight;

        // Добавляем новое сообщение в наблюдение и проверяем видимость
        if (window.messageObserver) {
            window.messageObserver.observe(newMessageElement);
            // Проверяем, видно ли сообщение сразу
            const rect = newMessageElement.getBoundingClientRect();
            const isVisible = rect.top >= 0 && rect.bottom <= window.innerHeight;
            if (isVisible && window.markAsReadDebounced) {
                console.log(`Сообщение ${event.message.id} уже в зоне видимости`);
                window.markAsReadDebounced(event.message.id);
            }
        } else {
            console.warn('messageObserver не найден в window, сообщение не будет отслеживаться для прочтения');
        }

        console.log(`Сообщение ${event.message.id} добавлено в открытый чат ${event.chat.id}`);
    } else {
        console.log('Чат не открыт или selectedChatId не совпадает с event.chat.id');
    }
}

// Основной обработчик события
function newMessageHandle(event) {
    console.log('Событие new-message-on-chat:', event);

    // Показываем тост
    showWarningToast(event.message.text, {
        url: event.chat.url,
        withSound: true,
        gravity: 'bottom',
        position: 'left'
    });

    // Обновляем счетчик непрочитанных чатов
    updateUnreadCount(event);

    // Обновляем список чатов
    updateChatList(event);

    // Обновляем открытый чат
    updateOpenChat(event);
}

// Обработчик события прочтения сообщения
window.addEventListener('message-read', (event) => {
    const { chatId, messageId } = event.detail;
    console.log('Получено событие message-read:', event.detail);
    updateChatListReadStatus(chatId, messageId);
});

window.newMessageHandle = newMessageHandle;


// Обработчик события прочтения сообщения
function messageReadHandle(event) {
    console.log('Сырое событие message-read от Echo:', event);
    window.dispatchEvent(new CustomEvent('message-read', {
        detail: {
            chatId: event.chatId,
            messageId: event.messageId
        }
    }));
}

window.messageReadHandle = messageReadHandle;
