@extends('layouts.main')

@section('title', $title)

@section('main.content')
    <x-form-container>
        <x-card.card>
            <x-card.header-nav :title="$title" :text="'Назад'" :url="route('admin.dashboard')"/>
            <x-card.body>
                <table class="table table-hover table-sm mb-0">
                    @if($backups->isNotEmpty())
                        <thead class="text-center">
                        <th>Папка</th>
                        <th>Файл</th>
                        <th>Скачать</th>
                        <th>Удалить</th>
                        <th>Восстановить</th>
                        </thead>
                        <tbody class="text-center align-middle">
                        @foreach($backups as $backup)
                            <tr>
                                <td>{{ $backup->dir }}</td>
                                <td>{{ $backup->file }}</td>
                                <td>
                                    <div class="col my-auto">
                                        <a class="m-2" href="{{ route('admin.backups.download', [$backup->dir, $backup->file]) }}">
                                            <i class="fa-solid fa-download fa-xl"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="col">
                                        <x-icon-modal-action
                                            action="{{route('admin.backups.delete', [$backup->dir, $backup->file])}}"
                                            text_body="Удалить бекап?"
                                        />
                                    </div>
                                </td>
                                <td><div class="col">
                                        <x-icon-modal-action
                                            action="{{route('admin.backups.restore', [$backup->dir, $backup->file])}}"
                                            text_body="Восстановить бекап: {{$backup->dir.'/'.$backup->file}}?"
                                            :method="'POST'"
                                            :icon="'fa-solid fa-database fa-xl'"
                                            :text_btn="'Восстановить'"
                                        />
                                    </div></td>

                            </tr>
                        @endforeach
                        </tbody>
                    @else
                        <tbody class="text-center align-middle">
                        <h3 class="text-center my-4">Список пуст.</h3>
                        </tbody>
                    @endif
                </table>
            </x-card.body>
            <x-card.footer>
                <x-link-button :href="route('admin.backups.create')" class="mb-2">
                    Создать
                </x-link-button>
            </x-card.footer>
        </x-card.card>
    </x-form-container>
@endsection








