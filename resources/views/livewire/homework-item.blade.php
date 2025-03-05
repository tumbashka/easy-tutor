<div class="row align-items-center py-2 text-center text-sm-start">
    <div class="col-sm-9 my-sm-auto mb-2">
        <p class="mb-0">{{ $homework->description }}</p>
    </div>

    <div class="col-sm-3 my-sm-auto mb-2 d-flex justify-content-around">
        <div class="align-self-center">
            <a href="{{ route('students.homeworks.edit', ['student' => $homework->student_id, 'homework' => $homework]) }}"
               class="me-2 d-inline link-underline link-underline-opacity-0">
                <div class="d-sm-none d-inline text-info">Редактировать</div>
                <i class="fa-solid fa-pen-to-square fa-xl"></i>
            </a>
        </div>
        <div class="align-self-center">
            <button type="button" class="btn text-info" data-bs-toggle="modal"
                    data-bs-target="#deleteModal{{ $homework->id }}">
                <div class="d-sm-none d-inline text-info">Удалить</div>
                <i class="fa-solid fa-trash-can fa-xl"></i>
            </button>

            <x-modal-dialog
                :text_body="'Удалить задание?'"
                :action="route('students.homeworks.destroy', ['student' => $homework->student_id, 'homework' => $homework])"
                :id="$homework->id"/>
        </div>
        <div class="align-self-center ms-1">
            <div class="d-sm-none d-inline text-info">
                @if($is_completed)
                    Выполнено
                @else
                    Не выполнено
                @endif
            </div>
            <input title="Отметить выполнение" class="form-check-input border border-2 m-0" style="width: 28px; height: 28px"
                   type="checkbox"
                   wire:change="toggleComplete"
                @checked($is_completed)>
        </div>
    </div>
</div>
