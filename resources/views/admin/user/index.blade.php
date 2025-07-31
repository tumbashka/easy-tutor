@extends('layouts.main')

@section('title', 'Список пользователей')

@section('main.content')
    <x-form-container>
        <x-card.card>
            <x-card.header-nav :title="'Список пользователей'" :text="'Назад'" :url="route('admin.dashboard')">
                <div class="col p-0 mt-2">
                    <div class="dropdown-center d-grid">
                        <x-button :size="'sm'" class="dropdown-toggle" data-bs-toggle="dropdown">
                            Фильтр
                        </x-button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item {{ isset($filter) ? '' : 'active' }}"
                                   href="{{ route('admin.users.index') }}">
                                    Все
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $filter == 'active' ? 'active' : '' }}"
                                   href="{{ route('admin.users.index', ['filter' => 'active']) }}">
                                    Активные
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $filter == 'not_active' ? 'active' : '' }}"
                                   href="{{ route('admin.users.index', ['filter' => 'not_active']) }}">
                                    Не активные
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $filter == 'admin' ? 'active' : '' }}"
                                   href="{{ route('admin.users.index', ['filter' => 'admin']) }}">
                                    Администраторы
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </x-card.header-nav>
            <x-card.body>
                <table class="table table-hover table-sm mb-0">
                    <thead class="text-center">
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Активен</th>
                    <th>Изменить</th>
                    </thead>
                    <tbody class="text-center align-middle">
                    @if(!empty($users))
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td class="{{ $user->email_verified_at ? 'text-success' : 'text-danger' }}">
                                    {{ $user->email }}
                                </td>
                                <td>
                                    {{ __($user->role->name) }}
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <i class="fa-solid fa-check text-success"></i>
                                    @else
                                        <i class="fa-solid fa-ban text-danger"></i>
                                    @endif
                                </td>
                                <td>
                                    @if(auth()->user()->can('update', $user))
                                        <a href="{{ route('admin.users.edit', $user) }}">
                                            <i class="fa-solid fa-pen-to-square fa-xl"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <h3 class="text-center mt-5">Список пользователей пуст.</h3>
                    @endif
                    </tbody>
                </table>
            </x-card.body>
            <x-card.footer>
                <x-link-button :href="route('admin.users.create')" class="mb-2">
                    Добавить пользователя
                </x-link-button>
                {{ $users->links() }}
            </x-card.footer>
        </x-card.card>
    </x-form-container>
@endsection








