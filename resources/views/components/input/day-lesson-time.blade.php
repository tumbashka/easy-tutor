@props([
    'day_num' => 0,
    'start' => '',
    'end' => '',
])

<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0">{{ getDayName($day_num) }}</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'end.'.$day_num"/>
        <x-form.input-error-alert :name="'start.'.$day_num"/>
        <div class="input-group">
            <span class="input-group-text">С</span>
            <input type="time" name="start[{{ $day_num }}]" class="form-control"
                   value="{{ old('start')[$day_num] ?? $start ?? '' }}"/>
            <span class="input-group-text" id="basic-addon1">До</span>
            <input type="time" name="end[{{ $day_num }}]" class="form-control" value="{{ old('end')[$day_num] ?? $end ?? '' }}"/>
        </div>
    </div>
</div>
