@extends('layouts.statistic')

@section('title', 'Статистика доходов по ученикам ')

@section('main.content')
    <x-form-container>
        <x-error-alert/>
        <x-statistic.selector
            :name="'Статистика доходов по ученикам'"
            :first_tab_name="'За период'"
            :second_tab_name="'Всё время'">
            <x-slot:first_tab_data_slot>
                <form action="{{ route('statistic.earnings.students_calculate') }}" method="POST">
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
                <form action="{{ route('statistic.earnings.students_calculate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="all" autocomplete="off">
                    <div class="row m-1">
                        <button type="submit" class="btn btn-primary">Сформировать</button>
                    </div>
                </form>
            </x-slot:second_tab_data_slot>
        </x-statistic.selector>
    </x-form-container>
    @isset($numbers)
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <x-chart.doughnut
                        :numbers="$numbers"
                        :labels="$labels"
                        :colors="$colors"
                    />
                </div>
            </div>
        </div>
    @endisset
@endsection








