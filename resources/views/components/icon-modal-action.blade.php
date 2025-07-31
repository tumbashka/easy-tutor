@props([
    'action' => '',
    'method' => 'DELETE',
    'text_btn' => 'Удалить',
    'text_head' => 'Подтвердите действие',
    'text_body' => 'Удалить ученика?',
    'id' => '',
    'icon' => 'fa-solid fa-trash-can fa-xl',
    'color' => 'text-info',
])
<div class="d-inline">
    <button type="button" class="btn {{$color}}" data-bs-toggle="modal" data-bs-target="#dialogModal{{ $action.$id }}">
        <i class="{{ $icon }}"></i>
    </button>
    <x-modal-dialog :text_button="$text_btn" :method="$method" :text_head="$text_head" :text_body="$text_body" :action="$action" :id="$action.$id"/>
</div>

