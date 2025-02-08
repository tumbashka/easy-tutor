@php use Illuminate\Support\Carbon; @endphp
@extends('layouts.main')

@section('title', 'Главная')

@section('main.content')
    <x-form-container>
        <x-card.card>
            <x-card.header-nav :text="'Назад'" :url="route('schedule.index', ['week' => getWeekOffset($day)])">
                <x-slot:title>
                    {{ getShortDayName($day) }}. {{ $day->translatedFormat('d F') }}
                </x-slot:title>
            </x-card.header-nav>
            <x-card.body>
                <table class="table table-hover table-sm mb-0">
                    <tbody class="text-center align-middle">
                    @php $empty = true; @endphp
                    @if(count($lessons))
                        @foreach($lessons as $lesson)
                            @if(!$lesson->is_canceled)
                                @php $empty = false; @endphp
                                <x-lesson.schedule-show-table-row :lesson="$lesson"/>
                            @endif
                        @endforeach
                    @endif
                    @if($empty)
                       <div class="text-center align-content-center" style="height: 60px;">
                           <h5>Занятий нет</h5>
                       </div>
                    @endif
                    </tbody>
                </table>
            </x-card.body>
            <x-card.footer>
                <x-link-button :href="route('schedule.lesson.create', ['day' => $day->format('Y-m-d')])">
                    Добавить занятие
                </x-link-button>
            </x-card.footer>
        </x-card.card>
        @php
            $empty = true;
            foreach ($lessons as $lesson){
                if($lesson->is_canceled){
                    $empty = false;
                    break;
                }
            }
        @endphp
        @if(!$empty)
            <x-card.card>
                <x-card.header :title="'Отменённые занятия'"/>
                <x-card.body>
                    <table class="table table-hover table-sm mb-0">
                        <tbody class="text-center align-middle">
                        @if(count($lessons))
                            @foreach($lessons as $lesson)
                                @if($lesson->is_canceled)
                                    <x-lesson.schedule-show-table-row :lesson="$lesson"/>
                                @endif
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </x-card.body>
                <x-card.footer>

                </x-card.footer>
            </x-card.card>
        @endif
    </x-form-container>

@endsection








