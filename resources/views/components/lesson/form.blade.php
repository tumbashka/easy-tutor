@props([
    'students' => null,
    'lesson' => null,
])

<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0 required-input">Ученик</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'student'"/>
        <x-input.student :old_student_id="$lesson->student_id ?? null" :students="$students"/>
    </div>
</div>
<hr>
<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0 required-input">Время</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'start'"/>
        <x-form.input-error-alert :name="'end'"/>
        <div class="input-group">
            <span class="input-group-text">С</span>
            <input name="start" type="time" class="form-control"
                   value="{{ old('start') ?? ($lesson != null ? $lesson->start->format('H:i') : '' )}}"/>
            <span class="input-group-text">До</span>
            <input name="end" type="time" class="form-control"
                   value="{{ old('end') ?? ($lesson != null ? $lesson->end->format('H:i') : '' ) }}"/>
        </div>
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
    <textarea class="form-control" style="height: 200px" name="note">{{ old('note')?? $lesson['note']?? '' }}</textarea>
    <label>Примечание</label>
</div>


