@props([
    'homework' => null,
])

<div class="row align-items-center">
    <div class="col">
        <div class="form-floating">
            <textarea class="form-control" style="height: 120px" name="description">{{ old('description')?? $homework->description ?? '' }}</textarea>
            <label>Краткое описание</label>
            <x-form.input-error-alert :name="'description'"/>
        </div>
    </div>
</div>


