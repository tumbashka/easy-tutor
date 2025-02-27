@props([
    'student' => null,
    'lesson_time' => null,
])

<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0 required-input">День недели</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'week_day'"/>
        <select name="week_day" class="form-select">
            @for($i = 0; $i <= 6; $i++)
                <option
                    {{ (old('week_day') == $i) ? 'selected' : (isset($lesson_time->week_day) && $i == $lesson_time->week_day ? 'selected' : '') }} value="{{ $i }}">{{ getDayName($i) }}
                </option>
            @endfor
        </select>
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
                   value="{{ old('start') ?? ($lesson_time != null ? $lesson_time->start->format('H:i') : '' )}}"/>
            <span class="input-group-text">До</span>
            <input name="end" type="time" class="form-control"
                   value="{{ old('end') ?? ($lesson_time != null ? $lesson_time->end->format('H:i') : '' ) }}"/>
        </div>
    </div>
</div>

