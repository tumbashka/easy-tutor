<div>
    <div class="row align-items-center mb-3">
        <div class="col-sm-3">
            <p class="mb-0">Дедлайн</p>
        </div>
        <div class="col-sm-9">
            <x-form.input-error-alert :name="'deadline'"/>
            <input wire:model.live="deadline" type="text" class="form-control datetime-picker"
                   placeholder="Бессрочно" id="deadline-input" name="deadline" value="{{ $deadline }}">
        </div>
    </div>
    <hr>

    @if($deadline)
        <div class="row align-items-center mb-3">
            <div class="col-sm-3">
                <label class="mb-0">Напоминания</label>
            </div>
            <div class="col-sm-9">
                <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input" id="reminderBeforeDeadline"
                           wire:model.live="reminderBeforeDeadline" name="reminderBeforeDeadline" value="1">
                    <label class="form-check-label" for="reminderBeforeDeadline">За N часов до дедлайна</label>
                </div>
                @if($reminderBeforeDeadline)
                    <div class="mt-2 ms-4">
                        <label for="reminderBeforeHours">За сколько часов:</label>
                        <input type="range" class="form-range" id="reminderBeforeHours" name="reminderBeforeHours"
                               wire:model.live="reminderBeforeHours" min="1" max="24" step="1">
                        <span>{{ $reminderBeforeHours }} {{ pluralRu($reminderBeforeHours, ['час', 'часа', 'часов']) }}</span>
                    </div>
                @endif

                <div class="form-check mb-2 mt-3">
                    <input type="checkbox" class="form-check-input" id="reminderDaily" name="reminderDaily"
                           wire:model.live="reminderDaily" value="1">
                    <label class="form-check-label" for="reminderDaily">Ежедневно в определенное время</label>
                </div>
                @if($reminderDaily)
                    <div class="mt-2 ms-4">
                        <label for="reminderDailyTime">Время:</label>
                        <input type="time" class="form-control" id="reminderDailyTime" name="reminderDailyTime"
                               wire:model.live="reminderDailyTime">
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
