@props([
    'task_category' => null,
])

<div class="row align-items-center py-2 text-center text-sm-start">
    <div class="col-sm-5 my-sm-2 mb-2">
        <p class="mb-0">{{ $task_category->name }}</p>
    </div>
    <div class="col-sm-5 my-sm-auto mb-2">
        <div class="rounded-2 border w-auto text-center {{ getTextContrastColor($task_category->color) }}" style="background-color: {{ $task_category->color }}">
            Цвет
        </div>
    </div>
    <div class="col-sm-2 my-sm-auto  mb-2 d-flex justify-content-around">
        <div class="align-self-center">
            <div class="d-sm-none d-inline text-info">
                Редактировать
            </div>
            <a href="{{ route('task_categories.edit', $task_category) }}"
               class="me-2 d-inline"><i class="fa-solid fa-pen-to-square fa-xl"></i></a>
        </div>
        <div>
            <div class="d-sm-none d-inline text-info">
                Удалить
            </div>
            <x-icon-modal-action
                :action="route('task_categories.destroy', $task_category)"
                :id="$task_category->id">
                <x-slot:text_body>
                    <p class="m-0">Удалить категорию?</p>
                </x-slot:text_body>
            </x-icon-modal-action>
        </div>
    </div>

</div>

