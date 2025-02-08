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
            <div class="d-sm-none d-inline text-info">
                Редактировать
            </div>
            <a href="{{ route('student.lesson-time.edit', ['student' => $lesson_time->student_id, 'lessonTime' => $lesson_time]) }}"
               class="me-2 d-inline"><i class="fa-solid fa-pen-to-square fa-xl"></i></a>
        </div>
        <div>
            <div class="d-sm-none d-inline text-info">
                Удалить
            </div>
            <x-icon-modal-delete
                :action="route('student.lesson-time.delete', ['student' => $lesson_time->student_id, 'lessonTime' => $lesson_time])"
                :text_body="'Удалить занятие?'"
                :id="$lesson_time->id"
            />
        </div>
    </div>

</div>

