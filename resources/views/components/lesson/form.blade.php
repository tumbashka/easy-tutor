@props([
    'students' => null,
    'lesson' => null,
    'occupiedSlots' => [],
])

<div class="row">
    <div class="col-sm-8">
        <div class="row align-items-center">
            <div class="col-sm-3">
                <p class="mb-0 required-input">Ученик</p>
            </div>
            <div class="col-sm-9">
                <x-form.input-error-alert :name="'student'"/>
                <x-input.student :old_student_id="old('student', $lesson?->student_id )" :students="$students"/>
            </div>
        </div>
        <hr>
        <div class="row align-items-center">
            <div class="col-sm-3">
                <p class="mb-0 required-input">Время</p>
            </div>
            <div class="col-sm-9">
                <x-lesson.timepicker :lesson="$lesson" :occupied-slots="$occupiedSlots" :students="$students"/>
            </div>
        </div>
        <hr>
        <div class="row align-items-center">
            <div class="col-sm-3">
                <p class="mb-0 required-input">Стоимость</p>
            </div>
            <div class="col-sm-9">
                <x-form.input-error-alert :name="'price'"/>
                <x-input.price :price="$lesson->price ?? ''"/>
            </div>
        </div>
        <hr>
        <div class="form-floating">
            <x-form.input-error-alert :name="'note'"/>
            <textarea class="form-control" style="height: 200px"
                      name="note">{{ old('note') ?? $lesson['note'] ?? '' }}</textarea>
            <label>Примечание</label>
        </div>
    </div>
    <div class="col-sm-4">
        <x-lesson.timeline :occupied-slots="$occupiedSlots" :lesson="$lesson"/>
    </div>
</div>
