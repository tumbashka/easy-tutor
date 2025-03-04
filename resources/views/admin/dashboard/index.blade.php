@extends('layouts.main')
@pushonce('css')
    @vite('resources/sass/admin.scss')
@endpushonce
@section('title', 'Список пользователей')

@section('main.content')
    <div class="row">
        <x-admin.dashboard-box
            :color="'#53FDCF'"
            :value="$users_count"
            :label="'Всего пользователей'"
            :icon="'fa-users'"
            :link_url="route('admin.users.index')"
        />
        <x-admin.dashboard-box
            :color="'#5F82DB'"
            :value="$students_count"
            :label="'Всего учеников'"
            :icon="'fa-children'"
            :link_url="'#'"
        />
        <x-admin.dashboard-box
            :color="'#F68E00'"
            :value="$lessons_count"
            :label="'Всего проведённых занятий'"
            :icon="'fa-person-chalkboard'"
            :link_url="'#'"
        />
        <x-admin.dashboard-box
            :color="'#CE1F1F'"
            :value="$last_registered->name"
            :label="'Новый пользователь'"
            :icon="'fa-user-plus'"
            :link_url="route('admin.users.edit', ['user' => $last_registered])"
        />
        <x-admin.dashboard-box
            :color="'#9BFF00'"
            :value="$not_active_users_count"
            :label="'Заблокированные пользователи'"
            :icon="'fa-user-check'"
            :link_url="route('admin.users.index', ['filter' => 'not_active'])"
        />
    </div>

@endsection








