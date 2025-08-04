<div class="align-self-center ms-1">
    <div class="d-sm-none d-inline text-primary">
        @if($is_completed)
            Выполнено
        @else
            Не выполнено
        @endif
    </div>
    <input title="Отметить выполнение" class="form-check-input border border-2 m-0" style="width: 28px; height: 28px"
           type="checkbox"
           wire:change="switch({{ $homework_id }})"
        @checked($is_completed)>
</div>
