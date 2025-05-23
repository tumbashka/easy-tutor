@props([
    'action' => '',
    'text_btn' => 'Удалить',
    'text_head' => 'Подтвердите удаление',
    'text_body' => 'Удалить ученика?',
    'id' => '',
    'icon' => 'fa-solid fa-trash-can fa-xl',
    'color' => 'text-info',
])
<div class="d-inline">
    <button type="button" class="btn {{$color}}" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $id }}">
        <i class="{{ $icon }}"></i>
    </button>
    <x-modal-dialog :text_body="$text_body" :action="$action" :id="$id"/>
</div>

