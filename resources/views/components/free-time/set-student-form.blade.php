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
        <x-input.student :students="$students"/>
    </div>
</div>



