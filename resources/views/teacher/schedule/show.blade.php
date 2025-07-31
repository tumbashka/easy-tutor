@php use Illuminate\Support\Carbon; @endphp
@extends('layouts.main')

@section('title', 'Главная')

@section('main.content')
    <x-form-container :second_col_classes="['col-sm-6', 'col-md-4', 'col-lg-2', 'col-xl-2', 'col-xxl-2', 'px-2',]">
        <div class="row">
            <div class="col">
                <x-card.card>
                    <x-card.header-nav :text="'Назад'" :url="route('schedule.index', ['week' => getWeekOffset($dayCarbon)])">
                        <x-slot:title>
                            {{ getShortDayName($dayCarbon) }}. {{ $dayCarbon->translatedFormat('d F') }}
                        </x-slot:title>
                    </x-card.header-nav>
                    <x-card.body>
                        <table class="table table-hover table-sm mb-0">
                            <tbody class="text-center align-middle">
                            @if($lessons->where('is_canceled')->count() == $lessons->count())
                                <div class="text-center align-content-center" style="height: 60px;">
                                    <h5>Занятий нет</h5>
                                </div>
                            @else
                                @foreach($lessons->where('is_canceled', false) as $lesson)
                                    <x-lesson.schedule-show-table-row :lesson="$lesson"/>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </x-card.body>
                    <x-card.footer>
                        <x-link-button :href="route('schedule.lesson.create', ['day' => $dayCarbon->format('Y-m-d')])">
                            Добавить занятие
                        </x-link-button>
                    </x-card.footer>
                </x-card.card>

                @if($lessons->where('is_canceled', true)->isNotEmpty())
                    <x-card.card>
                        <x-card.header :title="'Отменённые занятия'"/>
                        <x-card.body>
                            <table class="table table-hover table-sm mb-0">
                                <tbody class="text-center align-middle">
                                @foreach($lessons->where('is_canceled', true) as $lesson)
                                    @if($lesson->is_canceled)
                                        <x-lesson.schedule-show-table-row :lesson="$lesson"/>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </x-card.body>
                        <x-card.footer>
                        </x-card.footer>
                    </x-card.card>
                @endif
            </div>
        </div>
        <x-slot:second_col>
                <x-lesson.timeline :occupied-slots="$occupiedSlots"/>
        </x-slot:second_col>
    </x-form-container>

@endsection








