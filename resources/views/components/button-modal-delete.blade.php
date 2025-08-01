@props([
    'action' => '',
    'text_btn' => 'Удалить',
    'text_head' => 'Подтвердите удаление',
    'text_body' => 'Удалить ученика?',
    'id' => '',
])
<div class="shadow d-grid bg-primary bg-gradient rounded-2 border mb-3">
    <button type="button" class="btn btn-outline-light btn-xl mx-3 my-2 " data-bs-toggle="modal"
            data-bs-target="#dialogModal{{ $id }}">
        {{ $text_btn }}
    </button>
    <x-modal-dialog :method="'delete'" :text_body="$text_body" :action="$action" :id="$id"/>
</div>


