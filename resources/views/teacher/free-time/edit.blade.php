@extends('layouts.main')

@section('title', 'Редактирование окна')

@section('main.content')
    <x-form-container>
        <form class="m-0" action="{{ route('free-time.update', compact('free_time')) }}" method="post">
            @csrf
            @method('PUT')
            <x-card.card>
                <x-card.header-nav
                    :text="'Назад'"
                    :url="route('free-time.index')">
                    <x-slot:title>
                        Редактирование окна
                    </x-slot:title>
                </x-card.header-nav>
                <x-card.body>
                    <x-free-time.form-create :$free_time :$day/>
                </x-card.body>
                <x-card.footer>
                    <x-button type="submit">
                        Сохранить
                    </x-button>
                </x-card.footer>
            </x-card.card>
        </form>
        <x-button-modal-delete :text_body="'Удалить окно?'" :action="route('free-time.delete', $free_time)"/>
        <x-link-button-on-red :href="route('free-time.set-student', compact('free_time'))">Назначить ученика</x-link-button-on-red>
    </x-form-container>
@endsection








