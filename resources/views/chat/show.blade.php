@extends('layouts.main')

@section('title', $title)

@section('main.content')
    <div class="container ">
        <div class="row g-3">
            <!-- Список чатов -->
            <div class="col-md-4">
                <x-card class="card shadow" style="height: 80vh;">
                    <div class="card-header bg-primary text-white">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link active text-white" id="my-chats-tab" data-bs-toggle="tab"
                                   href="#my-chats" role="tab">Мои чаты</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" id="new-chats-tab" data-bs-toggle="tab" href="#new-chats"
                                   role="tab">Новые</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-0 tab-content" style="overflow-y: auto;">
                        <!-- Мои чаты -->
                        <div class="tab-pane fade show active" id="my-chats" role="tabpanel">
                            <ul class="list-group list-group-flush">
                                @foreach ($chats as $chat)
                                    <li class="list-group-item border-light-subtle d-flex align-items-center @if(isset($selectedChat) && $chat->id === $selectedChat->id) active @endif">
                                        <a href="{{ route('chat.show', $chat) }}"
                                           class="text-decoration-none d-flex align-items-center flex-grow-1 text-dark">
                                            <img src="{{ $chat->avatar_url ?? '/default-avatar.png' }}"
                                                 class="rounded-circle me-2" width="40" height="40" alt="avatar">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <strong>{{ $chat->name }}</strong>
                                                    <small>{{ $chat->lastMessage?->created_at->format('H:i') }}</small>
                                                </div>
                                                <div class="small">
                                                    @if($chat->lastMessage)
                                                        @if($chat->lastMessage->user_id === auth()->id())
                                                            <span class="me-2">
                                                                @if($chat->lastMessage->isRead())
                                                                    <i class="fas fa-check-double text-primary"></i>
                                                                @else
                                                                    <i class="fas fa-check text-primary"></i>
                                                                @endif
                                                            </span>
                                                        @elseif(!$chat->lastMessage->isReadByUser())
                                                            <i class="fas fa-circle text-primary fa-2xs"></i>
                                                        @endif
                                                        {{ Str::limit($chat->lastMessage->text, 25) }}
                                                    @else
                                                        @lang('Еще нет сообщений')
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown ms-2">
                                            <button class="btn btn-sm btn-link text-dark" type="button"
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Архивировать</a></li>
                                                <li><a class="dropdown-item" href="#">Удалить</a></li>
                                                <li><a class="dropdown-item" href="#">Отметить как непрочитанное</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- Новые чаты -->
                        <div class="tab-pane fade" id="new-chats" role="tabpanel">
                            <ul class="list-group list-group-flush">
                                @foreach ($newChats as $chat)
                                    <li class="list-group-item d-flex align-items-center @if(isset($selectedChat) && $chat->id === $selectedChat->id) active @endif">
                                        <a href="{{ route('chat.show', $chat) }}"
                                           class="text-decoration-none d-flex align-items-center flex-grow-1 text-dark">
                                            <img
                                                src="{{ $chat->users->first(fn($u) => $u->id != auth()->id())->avatar ?? '/default-avatar.png' }}"
                                                class="rounded-circle me-2" width="40" height="40" alt="avatar">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <strong>{{ $chat->name }}</strong>
                                                    <small>{{ $chat->lastMessage?->created_at->format('H:i') }}</small>
                                                </div>
                                                <div class="small">
                                                    {{ Str::limit($chat->lastMessage->text, 25) }}
                                                    <span class="badge bg-primary text-white ms-2">!</span>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown ms-2">
                                            <button class="btn btn-sm btn-link text-dark" type="button"
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('chat.accept', $chat) }}">Принять</a>
                                                </li>
                                                <li><a class="dropdown-item" href="#">Отклонить</a></li>
                                                <li><a class="dropdown-item" href="#">Заблокировать</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Чат -->
            <div class="col-md-8">
                <div class="card shadow" style="height: 80vh;">
                    @if (isset($selectedChat))
                        <div class="card-header  bg-primary text-white d-flex align-items-center"
                             style="padding-bottom: 9px;">
                            <h5 class="mb-0 flex-grow-1">
                                {{ $selectedChat->name ?? $selectedChat->users->first(fn($u) => $u->id != auth()->id())->name }}
                            </h5>
                            <div>
                                <button class="btn btn-sm btn-link text-white" title="Информация о чате">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                <button class="btn btn-sm btn-link text-white" title="Очистить чат">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-sm btn-link text-white" title="Дополнительно">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column p-3" style="overflow-y: auto;">
                            @foreach ($selectedChat->messages as $message)
                                <div
                                    class="message-wrapper @if($message->user_id === auth()->id()) sent @else received @endif">
                                    <div
                                        class="message-bubble @if($message->user_id === auth()->id()) sent @else received @endif">
                                        <div class="fw-bold small">
                                            {{ $message->user->name }}
                                        </div>
                                        <div class="my-1">{{ $message->text }}</div>
                                        <div class="message-meta">
                                            {{ $message->created_at->format('H:i') }}
                                            @if($message->user_id === auth()->id())
                                                @if($message->isRead())
                                                    <i class="fas fa-check-double ms-1"></i>
                                                @else
                                                    <i class="fas fa-check ms-1"></i>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="card-footer border-light-subtle bg-light">
                            <form action="{{ route('chat.message.store', $selectedChat) }}" method="POST">
                                @csrf
                                <div class="input-group rounded border border-light-subtle" style="min-height: 70px">
                                    <textarea name="text" class="form-control border-0"
                                              placeholder="Введите сообщение..." required
                                              style="resize: none;"></textarea>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="card-body d-flex align-items-center justify-content-center text-muted">
                            Выберите чат из списка слева
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@pushonce('css')
    <style>
        .message-bubble {
            max-width: 70%;
            padding: 0.75rem 1rem;
            border-radius: 1.25rem;
            font-size: 0.95rem;
            line-height: 1.5;
            word-wrap: break-word;
            position: relative;
        }

        .message-bubble.sent {
            background-color: rgba(255, 211, 218, 0.4);
            color: #212529;
            border-bottom-right-radius: 0.4rem;
        }

        .message-bubble.received {
            background-color: rgba(126, 67, 13, 0.10);
            color: #212529;
            border-bottom-left-radius: 0.4rem;
        }

        .message-bubble .message-meta {
            font-size: 0.75rem;
            margin-top: 0.4rem;
            opacity: 0.7;
        }

        .message-bubble.sent .message-meta {
            text-align: right;
        }

        .message-bubble.received .message-meta {
            text-align: left;
        }

        .message-wrapper {
            display: flex;
            margin-bottom: 1rem;
        }

        .message-wrapper.sent {
            justify-content: flex-end;
        }

        .message-wrapper.received {
            justify-content: flex-start;
        }

        .list-group-item.active {
            background-color: rgba(161, 47, 74, 0.1);
        }

        .list-group-item:hover {
            background-color: rgba(161, 47, 74, 0.1);
        }

        .card {
            border: none;
            border-radius: 0.75rem;
        }

        .card-header {
            border-bottom: 2px solid rgba(0, 0, 0, 0);
        }

        .nav-tabs .nav-link.active {
            color: #ffffff;
            border-color: transparent;
            border-bottom: 2px solid #ffffff;
            background: transparent;
        }

        .nav-tabs .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }

        .nav-tabs .nav-link:hover {
            color: #ffffff;
            border-color: transparent;
            border-bottom: 2px solid #ffffff;
        }

        .bg-primary {
            background-color: #a12f4a !important;
        }

        .text-primary {
            color: #a12f4a !important;
        }
    </style>
@endpushonce
