@props([
    'student' => null,
])

<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0 required-input">Имя</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'name'"/>
        <input type="text" autofocus name="name" value="{{ old('name')?? $student['name']?? '' }}" required
               class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}">
    </div>
</div>
<hr>
<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0 required-input">Класс</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'class'"/>
        <x-input.class :class="$student['class'] ?? null" :min="5"/>
    </div>
</div>
<hr>
<div class="row align-items-center">
    <div class="col-sm-3 d-flex ">
        <div class="mb-0 me-2 d-inline-block required-input">Стоимость</div>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'price'"/>
        <x-input.price :span="'руб./ч'" :price="$student['price']?? ''"/>
    </div>
</div>
<hr>
<div class="form-floating">
    <x-form.input-error-alert :name="'note'"/>
    <textarea class="form-control" style="height: 200px" name="note">{{ old('note')?? $student['note']?? '' }}</textarea>
    <label>Примечание</label>
</div>


