@props([
    'task' => null,
])

<div class="row justify-content-center">
    @foreach($task->task_categories as $category)
        <div class="col-auto pt-0 p-1 mb-2">
            <a class="link-underline link-underline-opacity-0"
               href="{{ route('tasks.index', ['task_category' => $category])}}">
                <span class="border border-1 badge large-badge {{ getTextContrastColor($category->color) }}"
                      style="background-color: {{ $category->color }}">{{ $category->name }}</span>
            </a>
        </div>
    @endforeach
</div>

<h5 class="text-center">
    {{ $task->title }}
</h5>
@if($task->description)
    <h6>Описание</h6>
    <hr class="mt-0 mb-1">
    <div class="row pt-1">
        <p class="text-muted text-start">{{ $task->description }}</p>
    </div>
@endif
@if($task->students->count())
    <hr class="mt-0 mb-1">
    <h6>Ученики</h6>
    <div class="row">
        @foreach($task->students as $student)
            <div class="col-auto pt-0 p-1 mb-2">
                <a class="link-underline link-underline-opacity-0"
                   href="{{ route('students.show', $student)}}">
                <span class="border border-1 border-dark badge text-dark">
                    {{ $student->name }}
                </span>
                </a>
            </div>
        @endforeach
    </div>
@endif
<hr class="mt-0 mb-1">
<div class="row pt-1">
    <div class="col-md-6">
        <h6>
            <i class="text-info fa-solid fa-calendar-clock fa-lg"></i>
            {{ \Illuminate\Support\Carbon::create($task->deadline)->translatedFormat('d F Yг. в H:i') }}

        </h6>
    </div>
    <div class="col-md-6">
        @if($task->completed_at)
            <h6>
                <i class="text-info fa-solid fa-calendar-check fa-lg"></i>
                {{ \Illuminate\Support\Carbon::create($task->completed_at)->translatedFormat('d F Yг. в H:i') }}
            </h6>
        @else
            <h6>
                <i class="text-info fa-solid fa-calendar-xmark fa-lg"></i>
                Не выполнено
            </h6>
        @endif
    </div>
</div>



