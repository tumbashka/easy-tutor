<input class="form-check-input m-0" type="checkbox" style="width: 20px; height: 20px" title="Отметить оплату"
        wire:change="switch({{ $lesson_id }})"
        @checked($isPaid)>

