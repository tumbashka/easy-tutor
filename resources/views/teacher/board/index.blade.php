@extends('layouts.main')

@vite('resources/js/tom-select.js')

@section('title', 'Доски')

@section('main.content')
    <x-form-container>
        <x-card.card>
            <x-card.header title='Электронные доски' title_size='h5'/>
            <x-card.body class="pb-2">
                <form class="m-0" action="{{ route('boards.index') }}" method="GET">
                    <div class="row mb-2">
                        <div class="col-sm-6">

                            <input type="text" name="name" value="{{ request()->get('name') }}"
                                   placeholder="Название доски"
                                   class="form-control form-control-sm {{ $errors->has('name') ? 'is-invalid' : '' }}">
                        </div>
                        <div class="col-sm-6">
                            <select name="subject_id" id="subject_id" data-tom-select-single
                                    placeholder="Выберите предмет" class="w-full form-select form-select-sm">
                                <option value="">Выберите предмет</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" @selected((int)request()->get('subject_id') === $subject->id)>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary btn-sm d-grid">Найти</button>
                    </div>
                </form>
            </x-card.body>

            <div class="position-relative d-flex justify-content-end px-4">

                <x-icon-modal-action
                    text_head="Новая доска"
                    text_btn="Создать"
                    :action="route('boards.store')"
                    method="POST"
                    color="btn-sm btn-primary"
                    icon='Создать'
                >
                    <div class="text-start">
                        <div class="mb-3">
                            <label class="form-label required-input">Название</label>
                            <input type="text" class="form-control"
                                   name="name" value="" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Предмет</label>
                            <select name="subject_id" class="form-select" data-tom-select-single>
                                <option value="">Не указан</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        @selected($subject->pivot->is_default)
                                    >
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </x-icon-modal-action>
            </div>
            <x-card.body>
                <div class="row">
                    <div class="col-5">Название</div>
                    <div class="col-4">Изменено</div>
                    <div class="col-3">Действия</div>
                </div>
                <hr class="mt-1">
                @forelse($boards as $board)
                    <div class="row align-items-center">
                        <div class="col-5">
                            {{ $board->name }}
                            @if($board->subject)
                                <span
                                    class="badge rounded-pill text-bg-success fw-normal text-white">{{ $board->subject->name }}</span>
                            @endif
                        </div>
                        <div class="col-4">
                            {{ $board->updated_at->format('d.m.Y H:i') }}
                        </div>
                        <div class="col-3">
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    Действия
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <button type="button" class="dropdown-item action-btn" data-bs-toggle="modal"
                                                data-bs-target="#dialogModal{{ 'update'.$board->id }}">
                                            Изменить
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item action-btn" data-bs-toggle="modal"
                                                data-bs-target="#dialogModal{{ 'copy'.$board->id }}">
                                            Копировать
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item action-btn" data-bs-toggle="modal"
                                                data-bs-target="#dialogModal{{ 'delete'.$board->id }}">
                                            Удалить
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <x-modal-dialog
                        method="PUT"
                        text_head="Редактирование"
                        :action="route('boards.update', $board)"
                        :id="'update' . $board->id"
                        text_button="Сохранить"
                    >
                        <div class="text-start">
                            <div class="mb-3">
                                <label class="form-label required-input">Название</label>
                                <input type="text" class="form-control board-name-input" name="name"
                                       value="{{ $board->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Предмет</label>
                                <select name="subject_id" class="form-select" data-tom-select-single>
                                    <option value="">Не указан</option>
                                    @foreach($subjects as $subject)
                                        <option
                                            value="{{ $subject->id }}" @selected($board->subject_id == $subject->id)>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </x-modal-dialog>
                    <x-modal-dialog
                        method="DELETE"
                        :action="route('boards.delete', $board)"
                        :id="'delete' . $board->id"
                    >
                        Удалить доску с именем: <b>{{$board->name}}</b> ?
                    </x-modal-dialog>

                    <x-modal-dialog
                        method="POST"
                        text_head="Копирование доски"
                        :action="route('boards.copy', $board)"
                        :id="'copy' . $board->id"
                        text_button="Создать"
                    >
                        <div class="text-start">
                            <div class="mb-3">
                                <label class="form-label required-input">Название</label>
                                <input type="text" class="form-control board-name-input" name="name"
                                       value="{{ $board->name }} Копия" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Предмет</label>
                                <select name="subject_id" class="form-select" data-tom-select-single>
                                    <option value="">Не указан</option>
                                    @foreach($subjects as $subject)
                                        <option
                                            value="{{ $subject->id }}" @selected($board->subject_id == $subject->id)>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </x-modal-dialog>
                    @if(!$loop->last)
                        <hr class="my-2">
                    @endif
                @empty
                    <h5 class="text-center">Список досок пуст</h5>
                @endforelse
            </x-card.body>
            <x-card.footer>
                {{ $boards->links() }}
            </x-card.footer>
        </x-card.card>
    </x-form-container>
@endsection

@pushonce('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // При клике на кнопку "Изменить"
            document.querySelectorAll('.action-btn').forEach(button => {
                button.addEventListener('click', function () {
                    // Закрываем dropdown
                    const dropdown = button.closest('.dropdown');
                    if (dropdown) {
                        const dropdownToggle = dropdown.querySelector('.dropdown-toggle');
                        const bsDropdown = bootstrap.Dropdown.getOrCreateInstance(dropdownToggle);
                        bsDropdown.hide();
                    }
                });
            });

            // При открытии модального окна устанавливаем фокус на поле ввода
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('shown.bs.modal', function () {
                    const input = modal.querySelector('.board-name-input');
                    if (input) {
                        input.focus();
                    }
                });
            });
        });
    </script>
@endpushonce
