@props([
    'students' => null,
    'free_time' => null,
    'day' => null,
])

<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0 required-input">День недели</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'week_day'"/>
        <select name="week_day" class="form-select {{ $errors->has('week_day') ? 'is-invalid' : '' }}">
            @for($i = 0; $i <= 6; $i++)
                <option {{ (old('week_day') == $i) ?
                'selected' : ($free_time != null && $i == $free_time->week_day ?
                'selected' : ($day == $i ? 'selected' : '')) }} value="{{ $i }}">
                    {{ getDayName($i) }}
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
            <input name="start" type="time" class="form-control {{ $errors->has('start') ? 'is-invalid' : '' }}"
                   value="{{ old('start') ?? ($free_time != null ? getHiFormatTime($free_time['start']) : '' )}}"/>
            <span class="input-group-text">До</span>
            <input name="end" type="time" class="form-control {{ $errors->has('end') ? 'is-invalid' : '' }}"
                   value="{{ old('end') ?? ($free_time != null ? getHiFormatTime($free_time['end']) : '' ) }}"/>
        </div>
    </div>
</div>
<hr>
<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0 required-input">Статус</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'status'"/>
        <select name="status" class="form-select {{ $errors->has('status') ? 'is-invalid' : '' }}">
            @foreach(\App\Enums\FreeTimeStatus::cases() as $free_time_status)
                <x-form.option form_name='status' :source_var="$free_time->status ?? null" :value="$free_time_status">
                    @lang($free_time_status->name)
                </x-form.option>
            @endforeach
        </select>
    </div>
</div>
<hr>
<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0 required-input">Вид занятия</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'type'"/>
        <select name="type" class="form-select {{ $errors->has('type') ? 'is-invalid' : '' }}">
            @foreach(\App\Enums\FreeTimeType::cases() as $free_time_type)
                <x-form.option form_name='type' :source_var="$free_time->type ?? null" :value="$free_time_type">
                    @lang($free_time_type->name)
                </x-form.option>
            @endforeach
        </select>
    </div>
</div>
<hr>
<div class="form-floating">
    <x-form.input-error-alert :name="'note'"/>
    <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" style="height: 200px" name="note">{{ old('note')?? $free_time->note ?? '' }}</textarea>
    <label>Примечание</label>
</div>


