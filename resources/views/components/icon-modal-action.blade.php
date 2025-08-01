@props([
    'action' => '',
    'method' => 'DELETE',
    'text_btn' => 'Удалить',
    'text_head' => 'Подтвердите действие',
    'text_body' => 'Удалить ученика?',
    'id' => '',
    'icon' => 'delete',
    'color' => 'text-primary',
])
<div class="d-inline">
    <button type="button" class="btn {{$color}}" data-bs-toggle="modal"
            data-bs-target="#dialogModal{{ $action.$method.$id }}">
        <i class="
        @switch($icon)
            @case('delete')
                fa-solid fa-trash-can fa-xl
            @break
            @case('edit')
                fa-solid fa-pen-to-square fa-xl
            @break
            @default
                {!! $icon !!}
        @endswitch
        "></i>
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

