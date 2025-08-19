// // Глобальные функции для наблюдения за сообщениями и отметки их как прочитанных
// window.initMessageObserver = function (chatBody, chatId) {
//     let latestSeenMessageId = null;
//
//     window.markAsReadDebounced = debounce((messageId) => {
//         if (messageId && chatId) {
//             console.log('Отправляем запрос на прочтение:', { chatId, messageId });
//             fetch(`/chat/${chatId}/message/${messageId}/read`, {
//                 method: 'POST',
//                 headers: {
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
//                     'Accept': 'application/json',
//                 },
//             }).then(response => {
//                 if (response.ok) {
//                     console.log(`Сообщение ${messageId} отмечено как прочитанное`);
//                     // Обновляем только сообщения в чате
//                     const messages = chatBody.querySelectorAll('.message-wrapper.unread');
//                     messages.forEach(msg => {
//                         const msgId = parseInt(msg.id.replace('message', ''));
//                         if (msgId <= messageId) {
//                             msg.classList.remove('unread');
//                             const unreadDiv = msg.querySelector('.unread-message');
//                             if (unreadDiv) unreadDiv.remove();
//                         }
//                     });
//                     // Не трогаем индикатор в списке чатов здесь, он обновляется через updateChatListReadStatus
//                 } else {
//                     console.error('Ошибка при отметке сообщения как прочитанного:', response.statusText);
//                 }
//             }).catch(err => {
//                 console.error('Ошибка запроса на прочтение:', err);
//             });
//         }
//     }, 500);
//
//     window.messageObserver = new IntersectionObserver(entries => {
//         entries.forEach(entry => {
//             if (entry.isIntersecting && entry.target.classList.contains('unread')) {
//                 console.log('Непрочитанное сообщение в зоне видимости:', entry.target.id);
//                 const messageId = parseInt(entry.target.id.replace('message', ''));
//                 if (!latestSeenMessageId || messageId > latestSeenMessageId) {
//                     latestSeenMessageId = messageId;
//                     window.markAsReadDebounced(messageId);
//                 }
//             }
//         });
//     }, { threshold: 0.5 });
//
//     // Наблюдаем за всеми существующими сообщениями
//     chatBody.querySelectorAll('.message-wrapper').forEach(msg => {
//         window.messageObserver.observe(msg);
//     });
// };
//
// function debounce(func, wait) {
//     let timeout;
//     return function (...args) {
//         clearTimeout(timeout);
//         timeout = setTimeout(() => func.apply(this, args), wait);
//     };
// }
