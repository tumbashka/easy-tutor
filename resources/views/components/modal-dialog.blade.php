@props([
    'id' => '',
    'text_head' => 'Подтвердите удаление',
    'text_button' => 'Удалить',
    'action' => '',
    'method' => ''
])

<div class="modal fade" id="dialogModal{{ $id }}" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content text-dark">
            <form action="{{ $action }}" method="post" id="dialogForm{{ $id }}">
                @csrf
                @method($method)
                <div class="modal-header">
                    <h1 class="modal-title fs-5">{{ $text_head }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {!! $slot !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">{{$text_button}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('dialogModal{{ $id }}');
            const form = document.getElementById('dialogForm{{ $id }}');

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
