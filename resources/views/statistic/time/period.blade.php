@extends('layouts.statistic')

@section('title', 'Статистика рабочего времени по периодам')

@section('main.content')
    <x-form-container>
        <x-error-alert/>
        <x-statistic.selector
            :name="'Статистика рабочего времени по периодам'"
            :first_tab_name="'По дням'"
            :second_tab_name="'По месяцам'">
            <x-slot:first_tab_data_slot>
                <form action="{{ route('statistic.time.period_calculate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="day" autocomplete="off">
                    <div class="row m-1 mb-3">
                        <input name="range" type="text" class="form-control range-picker" placeholder="Диапазон дат">
                    </div>
                    <div class="row m-1">
                        <button type="submit" class="btn btn-primary">Сформировать</button>
                    </div>
                </form>
            </x-slot:first_tab_data_slot>
            <x-slot:second_tab_data_slot>
                <form action="{{ route('statistic.time.period_calculate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="month" autocomplete="off">
                    <div class="row m-1 mb-3">
                        <input name="range" type="text" class="form-control month-range-picker"
                               placeholder="Диапазон дат">
                    </div>
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
                <div class="col-12 ">
                    <x-chart.bar :numbers="$numbers" :labels="$labels" :name="'myChart'" :label_data="$label"/>
                    <div class="text-center">
                        <h6>Всего: {{ $total }}</h6>
                    </div>
                </div>
            </div>
        </div>
    @endisset
@endsection








