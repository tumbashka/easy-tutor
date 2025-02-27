<input title="Отметить выполнение" class="form-check-input border border-2 m-0" style="width: 28px; height: 28px" type="checkbox"
        wire:change="switch({{ $task_id }})"
        @checked($is_completed)>
