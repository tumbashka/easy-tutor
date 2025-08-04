@extends('layouts.main')

@section('title', 'Предметы')

@section('main.content')
    <x-form-container>
        <x-card.card>
            <x-card.header-nav :title="'Список предметов'" :text="'Назад'" :url="route('admin.dashboard')">

            </x-card.header-nav>
            <x-card.body>
                <table class="table table-hover table-sm mb-0">
                    <thead class="text-center">
                    <th>Название</th>
                    <th>Активен</th>
                    <th>Изменить</th>
                    </thead>
                    <tbody class="text-center align-middle">
                    @if(!empty($subjects))
                        @foreach($subjects as $subject)
                            <tr>
                                <td>{{ $subject->name }}</td>
                                <td>
                                    @if($subject->is_active)
                                        <i class="fa-solid fa-check text-success"></i>
                                    @else
                                        <i class="fa-solid fa-ban text-danger"></i>
                                    @endif
                                </td>
                                <td>
                                    @if(auth()->user()->can('update', $subject))
                                        <x-icon-modal-action
                                            :action="route('admin.subjects.update', $subject)"
                                            method='PUT'
                                            text_btn='Сохранить'
                                            text_head='Редактирование предмета'
                                            icon='edit'
                                        >
                                            <div class="text-start">
                                                <div class="mb-3">
                                                    <label class="form-label">Название предмета</label>
                                                    <input type="text" class="form-control"
                                                           name="name" value="{{ $subject->name }}" required>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <label class="form-label">Активен</label>
                                                    <input class="form-check-input" type="checkbox" value="1"
                                                           name="is_active" @checked($subject->is_active)>
                                                </div>
                                            </div>

                                        </x-icon-modal-action>
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
                <x-icon-modal-action
                    class="d-grid"
                    :action="route('admin.subjects.store')"
                    method='POST'
                    color='btn-outline-light'
                    text_btn='Сохранить'
                    text_head='Добавление предмета'
                >
                    <x-slot:icon>
                        Добавить предмет
                    </x-slot:icon>
                    <div class="mb-3">
                        <label class="form-label">Название предмета</label>
                        <input type="text" class="form-control"
                               name="name" value="" required>
                    </div>
                    <div class="form-check form-switch">
                        <label class="form-label">Активен</label>
                        <input class="form-check-input" type="checkbox" value="1"
                               name="is_active" checked>
                    </div>
                </x-icon-modal-action>
                {{ $subjects->links() }}
            </x-card.footer>
        </x-card.card>
    </x-form-container>
@endsection








