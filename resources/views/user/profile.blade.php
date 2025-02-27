@extends('layouts.main')

@section('title', 'Профиль педагога')

@section('main.content')
    <x-form-container>
        <x-card.card>
            <x-card.header :title="'Профиль педагога'"/>
            <x-card.body>
                <x-user.profile-body :$user/>
            </x-card.body>
            <x-card.footer>
                @if(auth()->id() && auth()->user()->id == $user->id)
                    <x-link-button href="{{ route('user.edit', $user) }}">
                        Редактировать
                        <i class="fa-light fa-pen-to-square fa-lg"></i>
                    </x-link-button>
                @endif
            </x-card.footer>
        </x-card.card>
    </x-form-container>
@endsection








