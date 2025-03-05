@props([
    'title' => "Ученик: {$student->name}",
])
@extends('layouts.main')

@section('title', $title)

@section('main.content')
    <x-form-container>
        <x-card.card>
            <x-card.header-nav :title="$title" :url="route('students.index')">
                <x-slot:text>
                    Назад
                    <i class="fa-light fa-arrow-left-from-bracket fa-lg"></i>
                </x-slot:text>
            </x-card.header-nav>
            <x-card.body>
                <x-student.profile :student="$student"/>
            </x-card.body>
            <x-card.footer>
                <x-link-button href="{{ route('students.edit', $student) }}">
                    Редактировать
                    <i class="fa-light fa-pen-to-square fa-lg"></i>
                </x-link-button>
            </x-card.footer>
        </x-card.card>
        <x-card.card>
            <x-card.header
                :title="'Занятия'"
            />
            <x-card.body>
                @if(!$lesson_times->count())
                    <p class="text-center h5">
                        Список занятий пуст
                    </p>
                @else
                    @foreach($lesson_times as $lesson_time)
                        <x-student.lesson :$lesson_time />
                    @endforeach
                @endif
            </x-card.body>
            <x-card.footer>
                <x-link-button href="{{ route('students.lesson-times.create', $student) }}">
                    Добавить
                    <i class="fa-light fa-circle-plus fa-lg"></i>
                </x-link-button>
            </x-card.footer>
        </x-card.card>
        <x-slot:second_col>
            <x-card.card>
                <x-card.header :title="'Домашнее задание'"/>
                <x-card.body>
                    @if(!$homeworks->count())
                        <p class="text-center h5">
                            Список пуст
                        </p>
                    @else
                        @foreach($homeworks as $homework)
                            <x-student.homework :$homework />
                        @endforeach
                    @endif
                </x-card.body>
                <x-card.footer>
                    <x-link-button :href="route('students.homeworks.create', $student)">
                        Добавить
                    </x-link-button>
                </x-card.footer>
            </x-card.card>
            <x-card.card>
                <x-card.header :title="'Настройки напоминаний'"/>
                @if($reminder)
                    @livewire('reminder-settings', compact('student'))
                    <x-card.footer/>
                @elseif(auth()->user()->telegram_id)
                    <x-card.body>
                        <p>Для установки telegram уведомлений, добавьте бота <a
                                class="link-underline link-underline-opacity-0"
                                href="https://t.me/easy_tutor_helper_bot"><b>@easy_tutor_helper_bot</b></a> в группу с учеником и отправьте
                            команду <b>/set_student</b></p>
                    </x-card.body>
                    <x-card.footer/>
                @else
                    <x-card.body>
                        <p class="text">Telegram не подключен к вашему аккаунту. Пожалуйста, подключите Telegram в
                            настройках вашего профиля, чтобы использовать напоминания.</p>
                    </x-card.body>
                    <x-card.footer>
                        <x-link-button :href="route('user.index')">
                            Перейти в настройки профиля
                        </x-link-button>
                    </x-card.footer>
                @endif
            </x-card.card>
        </x-slot:second_col>
    </x-form-container>
@endsection








