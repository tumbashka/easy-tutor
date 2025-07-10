@props([
    'id' => '',
    'text_head' => 'Подтвердите удаление',
    'text_body' => 'Удалить ученика?',
    'action' => '',
])

<div class="modal fade" id="deleteModal{{ $id }}" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content text-dark">
            <div class="modal-header">
                <h1 class="modal-title fs-5">{{ $text_head }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                {!! $text_body !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form action="{{ $action }}" method="post" id="deleteForm{{ $id }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-primary">Удалить</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('deleteModal{{ $id }}');
            const form = document.getElementById('deleteForm{{ $id }}');

            modal.addEventListener('shown.bs.modal', function () {
                modal.focus();

                modal.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
