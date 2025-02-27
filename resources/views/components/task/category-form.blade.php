@props([
    'task_category' => null,
])

<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0 required-input">Название</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'name'"/>
        <input type="text" autofocus name="name" value="{{ old('name') ?? ($task_category->name ?? '' )}}" required
               class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}">
    </div>
</div>
<hr>
<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0">Цвет</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'color'"/>
        <input name="color" type="color" value="{{ old('color') ?? ($task_category->color ?? '#a12f4a' )  }}" class="rounded-2 border-secondary-subtle">
    </div>
</div>


