@props([
    'lesson_time' => null,
])

<div class="row align-items-center py-2 text-center text-sm-start">
    <div class="col-sm-3 my-sm-2 mb-2">
        <p class="mb-0">{{ getDayName($lesson_time->week_day) }}</p>
    </div>
    <div class="col-sm-7 my-sm-auto  mb-2">
        <div class="input-group">
            <span class="input-group-text">С</span>
            <input disabled type="time" class="form-control" value="{{ $lesson_time->start->format('H:i') }}"/>
            <span class="input-group-text">До</span>
            <input disabled type="time" class="form-control" value="{{ $lesson_time->end->format('H:i') }}"/>
        </div>
    </div>
    <div class="col-sm-2 my-sm-auto  mb-2 d-flex justify-content-around">
        <div class="align-self-center">
            <a href="{{ route('students.lesson-times.edit', ['student' => $lesson_time->student_id, 'lesson_time' => $lesson_time]) }}"
               class="me-2 d-inline link-underline link-underline-opacity-0">
                <div class="d-sm-none d-inline text-info">
                    Редактировать
                </div>
                <i class="fa-solid fa-pen-to-square fa-xl"></i></a>
        </div>
        <div>
            <button type="button" class="btn text-info" data-bs-toggle="modal"
                    data-bs-target="#deleteModal{{ $lesson_time->id }}">
                <div class="d-sm-none d-inline text-info">
                    Удалить
                </div>
                <i class="fa-solid fa-trash-can fa-xl"></i>
            </button>

            <x-modal-dialog
                :text_body="'Удалить занятие?'"
                :action="route('students.lesson-times.destroy', ['student' => $lesson_time->student_id, 'lesson_time' => $lesson_time])"
                :id="$lesson_time->id"/>
        </div>
    </div>

</div>

