@extends('layouts.main')

@vite('resources/js/tom-select.js')

@section('title', $title)

@section('main.content')
    <x-form-container>
        <x-card.card>
            <x-card.header title='{{ $title }}' title_size='h5'/>
            <x-card.body class="pb-2">
                <form class="mb-3" action="{{ route('student.teachers.index') }}" method="GET">
                    <div class="row mb-2">
                        <div class="col-sm-4 mb-sm-0 mb-2 pe-sm-1">
                            <input type="text" name="name" value="{{ request()->get('name') }}"
                                   placeholder="Имя"
                                   class="form-control form-control-sm {{ $errors->has('name') ? 'is-invalid' : '' }}">
                        </div>
                        <div class="col-sm-4 mb-sm-0 mb-2 px-sm-1">
                            <select name="subjects[]" id="subjects" data-tom-select-multiple multiple
                                    placeholder="Выберите предметы" class="w-full form-select form-select-sm">
                                <option value="">Выберите предметы</option>
                                @foreach($subjects as $subject)
                                    <option
                                        @selected(request()->get('subjects') && in_array($subject->id, request()->get('subjects')))
                                        value="{{ $subject->id }}">
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4 mb-sm-0 mb-2 ps-sm-1">
                            <select name="days[]" id="days" data-tom-select-multiple multiple
                                    placeholder="Свободные дни" class="w-full form-select form-select-sm">
                                <option value="">Свободные дни</option>
                                @for($i = 0; $i < 7 ; $i++)
                                    <option @selected(request()->get('days') && in_array($i, request()->get('days')))
                                            value="{{ $i }}">
                                        {{ getDayName($i) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 mb-sm-0 mb-2 pe-sm-1">
                            <select name="sort" id="sort" data-tom-select-single
                                    placeholder="Сортировка" class="w-full form-select form-select-sm">
                                @foreach($sorting as $sort)
                                    <option @selected(request()->get('sort') === $sort)
                                            value="{{ $sort }}">
                                        @lang($sort)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4 mb-sm-0 mb-2 px-sm-1 d-grid">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Применить</button>
                        </div>
                        <div class="col-sm-4 mb-sm-0 mb-2 ps-sm-1 d-grid">
                            <a href="{{ route('student.teachers.index') }}" class="btn btn-outline-primary btn-sm">Сбросить фильтр</a>
                        </div>
                    </div>
                </form>

                @forelse($teachers as $teacher)
                    <x-card>
                        <x-card.body>
                            <div class="row g-3">
                                <div class="col-3 col-md-2 d-flex align-items-center">
                                    <img src="{{ $teacher->avatar_url }}" alt="Avatar" class="rounded-circle border"
                                         style="width: 85px; height: 85px;">
                                </div>
                                <div class="col-9 col-md-10">
                                    <div class="mb-2">
                                        <a href="{{ route('user.show', $teacher) }}"
                                           class="text-decoration-none fw-bold fs-5">
                                            {{ $teacher->name }}
                                            @if($teacher->age)
                                                <span
                                                    class="text-muted fs-6">({{ $teacher->age }} {{ pluralRu($teacher->age, ['год', 'года', 'лет']) }})</span>
                                            @endif
                                        </a>
                                    </div>
                                    @if($teacher->subjects->isNotEmpty())
                                        <div class="mb-2">
                                            <small class="text-muted">Предметы:</small><br>
                                            @foreach($teacher->subjects as $subject)
                                                <span
                                                    class="badge rounded-pill text-bg-primary text-light fw-normal mb-1">
                                                    {{ $subject->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <small class="text-muted">Провел занятий:</small><br>
                                            <span class="fw-medium">{{ $teacher->lessons_count }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Свободно мест:</small><br>
                                            <span class="fw-medium">{{ $teacher->free_times_count }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-card.body>
                    </x-card>
                @empty
                    <h5 class="text-center my-3">Преподаватели не найдены</h5>
                @endforelse
            </x-card.body>
            <x-card.footer>
                {{ $teachers->appends(request()->query())->links() }}
            </x-card.footer>
        </x-card.card>
    </x-form-container>
@endsection
