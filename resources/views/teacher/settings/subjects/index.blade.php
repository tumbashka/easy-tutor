@extends('layouts.main')

@section('title', 'Предметы')

@section('main.content')
    <x-form-container>
        <!-- Список предметов -->
        <x-card>
            <x-card.header-nav
                :title="'Все доступные предметы'"
                :url="route('user.settings.index')"
                :text="'К настройкам'"
            />
            <x-card.body>
                @foreach($subjects as $subject)
                    <div class="row">
                        <div class="col">
                            {{ $subject->name }}
                            @if($userSubjects->contains('name', $subject->name))
                                <span class="badge rounded-pill text-bg-success fw-normal text-white">Добавлен</span>
                            @endif
                        </div>
                        <div class="col-4">
                            @if($userSubjects->contains('name', $subject->name))
                                <x-link-button :href="route('user.settings.subjects.remove', $subject)"
                                               :color="'danger'"
                                               class="btn-sm text-light">
                                    Убрать
                                </x-link-button>
                            @else
                                <x-link-button :href="route('user.settings.subjects.add', $subject)"
                                               :color="'success'"
                                               class="btn-sm text-light">
                                    Добавить
                                </x-link-button>
                            @endif
                        </div>
                    </div>
                    @if(!$loop->last)
                        <hr class="my-2">
                    @endif
                @endforeach
            </x-card.body>
            <x-card.footer>
                {{ $subjects->links() }}
            </x-card.footer>
        </x-card>
    </x-form-container>
@endsection
