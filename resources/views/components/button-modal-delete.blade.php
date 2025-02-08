@props([
    'action' => '',
    'text_btn' => 'Удалить',
    'text_head' => 'Подтвердите удаление',
    'text_body' => 'Удалить ученика?',
    'id' => '',
])
<div class="shadow d-grid bg-info bg-gradient rounded-2 border">
    <button type="button" class="m-2 btn btn-outline-light btn-xl mx-4 my-3 " data-bs-toggle="modal"
            data-bs-target="#deleteModal{{ $id }}">
        {{ $text_btn }}
    </button>
    <x-modal-dialog :text_body="$text_body" :action="$action" :id="$id"/>
</div>


