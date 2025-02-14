@extends('layouts.main')

@section('title', 'Назначение ученика ')

@section('main.content')
    <x-form-container>
        <form action="{{ route('free-time.set-student-process', ['free_time' => $freeTime]) }}"
              method="post">
            @csrf
            <x-card.card>
                <x-card.header-nav
                    :text="'Назад'"
                    :url="route('free-time.edit', ['free_time' => $freeTime])">
                    <x-slot:title>
                        Назначение ученика {{ getShortDayName($freeTime->week_day) }}.
                        {{ getHiFormatTime($freeTime->start) }}-{{ getHiFormatTime($freeTime->end) }}
                    </x-slot:title>
                </x-card.header-nav>
                <x-card.body>
                    <x-free-time.set-student-form :$students/>
                </x-card.body>
                <x-card.footer>
                    <x-link-button class="mb-2" :href="route('student.create', ['free_time' => $freeTime])">Добавить нового ученика</x-link-button>
                    <x-button type="submit">
                        Назначить
                    </x-button>
                </x-card.footer>
            </x-card.card>
        </form>
    </x-form-container>
@endsection








