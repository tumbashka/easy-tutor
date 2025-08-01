@props([
    'student' => null,
])
<div class="row g-0">
    <div class="col-md-4 text-center align-content-center">
        <i class="text-primary fa-duotone fa-solid fa-user-graduate fa-flip-horizontal fa-5x"></i>
        <h4>{{ $student->name }}</h4>
    </div>
    <div class="col-md-8">
        <div class="card-body p-4 text-center">
            <h6>Информация</h6>
            <hr class="mt-0 mb-4">
            <div class="row pt-1 ">
                <div class="col-6 mb-3">
                    <h6>Класс</h6>
                    <p class="text-muted">{{ $student->class }}</p>
                </div>
                <div class="col-6 mb-3">
                    <h6>Стоимость</h6>
                    <p class="text-muted">{{ $student->price }}</p>
                </div>
            </div>
            <h6>Примечание</h6>
            <hr class="mt-0 mb-4">
            <div class="row pt-1">
                <p class="text-muted text-start">{{ $student->note }}</p>
            </div>
        </div>
    </div>
</div>





