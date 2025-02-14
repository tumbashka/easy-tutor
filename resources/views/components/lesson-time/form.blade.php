@props([
    'student' => null,
    'lessonTime' => null,
])

<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0">День недели</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'week_day'"/>
        <select name="week_day" class="form-select">
            @for($i = 0; $i <= 6; $i++)
                <option
                    {{ (old('week_day') == $i) ? 'selected' : (isset($lessonTime->week_day) && $i == $lessonTime->week_day ? 'selected' : '') }} value="{{ $i }}">{{ getDayName($i) }}
                </option>
            @endfor
        </select>
    </div>
</div>
<hr>
<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0">Время</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'start'"/>
        <x-form.input-error-alert :name="'end'"/>
        <div class="input-group">
            <span class="input-group-text">С</span>
            <input name="start" type="time" class="form-control"
                   value="{{ old('start') ?? ($lessonTime != null ? $lessonTime->start->format('H:i') : '' )}}"/>
            <span class="input-group-text">До</span>
            <input name="end" type="time" class="form-control"
                   value="{{ old('end') ?? ($lessonTime != null ? $lessonTime->end->format('H:i') : '' ) }}"/>
        </div>
    </div>
</div>

