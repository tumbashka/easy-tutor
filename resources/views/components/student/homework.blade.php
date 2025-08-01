@props([
    'homework' => null,
])

<div class="row align-items-center py-2 text-center text-sm-start">
    <div class="col-sm-9 my-sm-auto mb-2">
        <p class="mb-0">{{ $homework->description }}</p>
    </div>

    <div class="col-sm-3 my-sm-auto  mb-2 d-flex justify-content-around">
        <div class="align-self-center">
            <a href="{{ route('students.homeworks.edit', ['student' => $homework->student_id, 'homework' => $homework]) }}"
               class="me-2 d-inline link-underline link-underline-opacity-0">
                <div class="d-sm-none d-inline text-primary">
                    Редактировать
                </div>
                <i class="fa-solid fa-pen-to-square fa-xl"></i></a>
        </div>
        <div class="align-self-center">
            <button type="button" class="btn text-primary" data-bs-toggle="modal"
                    data-bs-target="#deleteModal{{ $homework->id }}">
                <div class="d-sm-none d-inline text-primary">
                    Удалить
                </div>
                <i class="fa-solid fa-trash-can fa-xl"></i>
            </button>

            <x-modal-dialog
                :text_body="'Удалить задание?'"
                :action="route('students.homeworks.destroy', ['student' => $homework->student_id, 'homework' => $homework])"
                :id="$homework->id"/>
        </div>
        <livewire:homework-complete-switcher :homework_id="$homework->id"
                                             :is_completed="(bool)$homework->completed_at"/>
    </div>

</div>

