<x-card.body>
    <h5 class="card-title">Настройки напоминаний</h5>
    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" wire:model.live="is_enabled" id="remindersEnabled">
        <label class="form-check-label" for="remindersEnabled">
            @if($is_enabled)
                Напоминания в Telegram включены
            @else
                Напоминания в Telegram выключены
            @endif
        </label>
    </div>
    @if($is_enabled)
        <div class="mb-3">
            <label for="minutesBefore" class="form-label">За сколько минут до начала урока</label>
            <input type="number" class="form-control" wire:model.live="before_lesson_minutes" id="minutesBefore"
                   min="0">
            @error('before_lesson_minutes') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="mb-3">
            <label for="dailyReminderTime" class="form-label">Время ежедневного напоминания о ДЗ</label>
            <input type="time" class="form-control" wire:model.live="homework_reminder_time" id="dailyReminderTime">
            @error('homework_reminder_time') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="mb-3">
            <p>Для смены группы с уведомлениями, добавьте в новую группу бота <a
                    class="link-underline link-underline-opacity-0" href="https://t.me/easy_tutor_helper_bot"><b>@easy_tutor_helper_bot</b></a>
                и отправьте команду <b>/set_student</b></p>
        </div>
    @endif
</x-card.body>
