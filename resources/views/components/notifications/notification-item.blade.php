@props([
    'notification' => null,
])
<div class="list-group-item @if(!$notification->read_at) unread bg-primary bg-opacity-10 @endif d-flex align-items-start"
     id="{{ $notification->id }}">
    <div class="notification-icon">
        <i class="fas fa-envelope text-primary fa-lg"></i>
    </div>
    <div class="flex-grow-1">
        <div class="fw-bold">
            @if($notification->data['url'])
                <a href="{{ $notification->data['url'] }}" class="link-underline link-underline-opacity-0">
                    {!! $notification?->data['text'] !!}
                </a>
            @else
                {!! $notification?->data['text'] !!}
            @endif
        </div>
        <small class="text-muted">
            {{ $notification->created_at->diffForHumans(['parts' => 2, 'short' => true]) }}
        </small>
    </div>
    @if(!$notification->read_at)
        <x-unread-span/>
    @endif
</div>
