@extends('layouts.statistic')

@section('title', 'Статистика занятий по студентам')

@section('main.content')
    <x-form-container>
        <x-error-alert/>
        <x-statistic.selector
            :name="'Статистика занятий по студентам'"
            :first_tab_name="'За период'"
            :second_tab_name="'Всё время'">
            <x-slot:first_tab_data_slot>
                <form action="{{ route('statistic.lessons.students_calculate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="month" autocomplete="off">
                    <div class="row m-1 mb-3">
                        <input name="range" type="text" class="form-control month-range-picker" placeholder="Диапазон дат">
                    </div>
                    <div class="row m-1">
                        <button type="submit" class="btn btn-primary">Сформировать</button>
                    </div>
                </form>
            </x-slot:first_tab_data_slot>
            <x-slot:second_tab_data_slot>
                <form action="{{ route('statistic.lessons.students_calculate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="all" autocomplete="off">
                    <div class="row m-1">
                        <button type="submit" class="btn btn-primary">Сформировать</button>
                    </div>
                </form>
            </x-slot:second_tab_data_slot>
        </x-statistic.selector>
    </x-form-container>
    @isset($labels)
        <div class="container">
            <div class="row">
                <div class="col-12 ">
                    <x-chart.two_bars_group
                        :labels="$labels"
                        :first_name="'Проведенные'"
                        :second_name="'Отменённые'"
                        :first_data="$first_data"
                        :second_data="$second_data"
                        :y_name="'Занятия'"
                    />
                </div>
            </div>
        </div>
    @endisset
@endsection








