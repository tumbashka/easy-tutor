<input class="form-check-input" type="checkbox"
        wire:change="switch({{ $lesson_id }})"
        @checked($isPaid)>

