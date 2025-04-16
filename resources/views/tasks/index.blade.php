@extends('layouts.main')

@section('title', 'Список дел')

@section('main.content')
    <x-header-two-nav
        :title="'Меню задач'"
        :left_url="route('schedule.index')"
        :right_url="route('tasks.create')">
        <x-slot:left_text>
            На главную
            <i class="fa-light fa-home fa-lg"></i>
        </x-slot:left_text>
        <x-slot:right_text>
            Добавить
            <i class="fa-light fa-circle-plus fa-lg"></i>
        </x-slot:right_text>
        <div class="row mt-2 align-items-center">
            <div class="col-10 pe-1">
                <div class="dropdown-center d-grid">
                    <x-button :size="'sm'" class="dropdown-toggle" data-bs-toggle="dropdown">
                        {{ $category_name ?? 'Выберите категорию' }}
                    </x-button>
                    <ul class="dropdown-menu">
                        @foreach($task_categories as $task_category)
                            <li>
                                <a class="dropdown-item {{ getTextContrastColor($task_category->color) }}"
                                   href="{{ route('tasks.index', compact('task_category')) }}"
                                   style="background-color: {{$task_category->color}}">
                                    {{ $task_category->name }}
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <a class="dropdown-item" href="{{ route('task_categories.create') }}">
                                Создать категорию
                                <i class="fa-light fa-circle-plus"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-2 ps-1">
                <x-link-button :size="'sm'" :href="route('task_categories.index')">
                    <i class="fa-light fa-list fa-lg"></i>
                </x-link-button>
            </div>
        </div>
    </x-header-two-nav>
    <x-form-container>
        <x-card.card>
            <x-card.header>
                <x-slot:title>
                    @if(isset($category_name))
                        {{ $category_name }}
                        <a href="{{ route('tasks.index') }}" class="link-light">
                            <i class="fa-regular fa-rectangle-xmark fa-lg"></i>
                        </a>
                    @else
                        Задачи
                    @endif
                </x-slot:title>
            </x-card.header>
            <x-card.body>
                @if($tasks->count())
                    @foreach($tasks as $task)
                        <div class="row align-items-center">
                            <div class="col-4 ps-2 ps-sm-4">
                                <a class="link-underline link-underline-opacity-25 link-underline-opacity-75-hover"
                                   href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a>
                            </div>
                            <div class="col-3 p-0 px-2 text-center">
                                @foreach($task->task_categories as $category)
                                    <a class="link-underline link-underline-opacity-0"
                                       href="{{ route('tasks.index', ['task_category' => $category])}}">
                                        <span class="badge {{ getTextContrastColor($category->color) }}"
                                              style="background-color: {{ $category->color }}">{{ $category->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                            <div class="col-3 pe-1">
                                @if($task->deadline)
                                    <div class="border border-info border-2 rounded-2 text-center ">
                                        <i class="fa-regular fa-hourglass-clock "></i>
                                        {{ \Illuminate\Support\Carbon::create($task->deadline)->diffForHumans(['parts' => 2, 'short' => true]) }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-1 p-0 pe-1 text-center">
                                <livewire:task-complete-switcher :task_id="$task->id"
                                                                 :is_completed="(bool)$task->completed_at"/>

                            </div>
                            <div class="col-1 p-0">
                                <x-icon-modal-delete
                                    :id="$task->id"
                                    :action="route('tasks.destroy', $task)"
                                    :text_body="'Удалить задачу?'"
                                    :icon="'fa-solid fa-trash-can fa-xl'"
                                />
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr class="m-2">
                        @endif
                    @endforeach
                @else
                    <p class="text-center h5">
                        Список задач пуст
                    </p>
                @endif
            </x-card.body>
            <x-card.footer class="text-light">
                {{ $tasks->links() }}
            </x-card.footer>
        </x-card.card>
    </x-form-container>
@endsection


