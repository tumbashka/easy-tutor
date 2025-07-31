@props([
    'roles' => null,
    'selected_role' => null,
])

<select name="role" id="role" data-tom-select-single placeholder="Выберите роль"
        {{$attributes->class(['w-full', 'form-select'])}}>
    <option value="">Выберите роль</option>
    @foreach($roles as $role)
        <option value="{{ $role->value }}" @selected($selected_role == $role)>
            {{ __($role->name) }}
        </option>
    @endforeach
</select>
