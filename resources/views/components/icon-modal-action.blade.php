@props([
    'action' => '',
    'type' => 'icon',
    'method' => 'DELETE',
    'text_btn' => 'Удалить',
    'text_head' => 'Подтвердите действие',
    'id' => '',
    'icon' => '<i class="fa-solid fa-trash-can fa-xl"></i>',
    'color' => 'text-primary',
])
<div {{ $attributes->merge(['class' => 'd-inline']) }}>
    <button type="button" class="btn {{ $color }}" data-bs-toggle="modal"
            data-bs-target="#dialogModal{{ $action.$method.$id }}">
        @switch($icon)
            @case('delete')
                <i class="fa-solid fa-trash-can fa-xl"></i>
                @break
            @case('edit')
                <i class="fa-solid fa-pen-to-square fa-xl"></i>
                @break
            @default
                {!! $icon !!}
        @endswitch
    </button>
    <x-modal-dialog
        :text_button="$text_btn"
        :method="$method"
        :text_head="$text_head"
        :action="$action"
        :id="$action.$method.$id"
    >
        {!! $slot !!}
    </x-modal-dialog>
</div>

