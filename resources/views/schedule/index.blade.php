@extends('layouts.main')

@section('title', 'Главная')

@section('main.content')
    <x-week-selector
        :week-offset="$weekOffset"
        :next="$next"
        :previous="$previous"
    />
    <x-week-statistics :statistics="$statistics"/>
    <x-week
        :lessonsOnDays="$lessonsOnDays"
        :weekDays="$weekDays"
    />
@endsection
