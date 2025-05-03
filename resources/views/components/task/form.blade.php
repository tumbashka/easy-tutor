@props([
    'task' => null,
    'task_categories' => null,
    'students_on_classes' => null,
])

<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0">Категории</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'categories'"/>
        @if(isset($task_categories) && $task_categories->count())
            <select name="categories[]" data-tom-select multiple placeholder="Выберите категории" class="w-full">
                @foreach($task_categories as $task_category)
                    <option value="{{ $task_category->id }}" data-color="{{ $task_category->color }}"
                        {{ old('categories') ? (in_array($task_category->id, old('categories')) ? 'selected' : '') : (isset($task->task_categories) && $task->task_categories->contains($task_category->id) ? 'selected' : '') }}>
                        {{ $task_category->name }}
                    </option>
                @endforeach
            </select>
        @else
            <input class="form-control" readonly value="Ещё не создано ни одной категории!">
        @endif
    </div>
</div>
<hr>

<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0 required-input">Заголовок</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'title'"/>
        <input type="text" name="title" value="{{ old('title')?? $task['title']?? '' }}" required
               class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}">
    </div>
</div>
<hr>
<div class="form-floating">
    <x-form.input-error-alert :name="'description'"/>
    <textarea class="form-control" style="height: 200px"
              name="description">{{ old('description')?? $task['description']?? '' }}</textarea>
    <label>Описание</label>
</div>
<hr>
<div class="row align-items-center">
    <div class="col-sm-3">
        <p class="mb-0">Ученики</p>
    </div>
    <div class="col-sm-9">
        <x-form.input-error-alert :name="'students'"/>
        @if(isset($students_on_classes) && $students_on_classes->count())
            <select name="students[]" data-tom-select-group multiple placeholder="Прикрепите учеников к задаче"
                    class="w-full">
                @foreach ($students_on_classes as $class => $students)
                    <option class="class-option" data-group="{{ $class }}" value="group-{{ $class }}">{{ $class }}
                        класс
                    </option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}" data-group="{{ $class }}"
                            {{ old('students') ? (in_array($student->id, old('students')) ? 'selected' : '') : (isset($task->students) && $task->students->contains($student->id) ? 'selected' : '') }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                @endforeach
            </select>
        @else
            <input class="form-control" readonly value="Ещё не создано ни одного ученика!">
        @endif
    </div>
</div>
<hr>


<livewire:reminder-settings :task="$task" />
