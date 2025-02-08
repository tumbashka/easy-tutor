@props([
    'action' => '',
    'text_btn' => 'Удалить',
    'text_head' => 'Подтвердите удаление',
    'text_body' => 'Удалить ученика?',
    'id' => '',
])
<div class="d-inline">
    <button type="button" class="btn text-info" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $id }}">
        <i class="fa-solid fa-trash-can fa-xl"></i>
    </button>
    <x-modal-dialog :text_body="$text_body" :action="$action" :id="$id"/>
</div>

