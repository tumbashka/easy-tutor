@props([
    'weekOffset' => '',
    'previous' => '',
    'next' => '',
])
<div class="row justify-content-center mb-3">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4 px-3 py-1 bg-info bg-gradient shadow rounded">
        <div class="row">
            <div class="col-4 p-1 d-inline-grid">
                <a class="btn btn-sm btn-outline-light" href="{{ route('schedule.index', ['week' => $weekOffset-1]) }}" role="button">
                    <i class="fa-duotone fa-thin fa-backward"></i>
                </a>
            </div>
            <div class="col-4 p-0 align-content-center">
                <p class="text-light text-center m-0">
                    <ins>{{ getWeekBorders($weekOffset) }}</ins>
                </p>
            </div>
            <div class="col-4 p-1 d-inline-grid">
                <a class="btn btn-sm btn-outline-light" href="{{ route('schedule.index', ['week' => $weekOffset+1]) }}" role="button">
                    <i class="fa-duotone fa-thin fa-forward"></i>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-4 p-1 d-inline-grid">
                <div class="dropdown d-inline-grid">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Прошлые
                    </button>
                    <ul class="dropdown-menu">
                        @foreach($previous as $offset => $weekBorder)
                            <li><a class="dropdown-item" href="{{ route('schedule.index', ['week' => $offset]) }}">{{ $weekBorder }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col-4 px-2 align-content-center text-center text-light d-inline-grid">
                <a class="btn btn-sm btn-outline-light" href="{{ route('schedule.index', ['week' => 0]) }}" role="button">
                    Текущая
                    <i class="fa-regular fa-solid fa-house fa-lg"></i>
                </a>
            </div>
            <div class="col-4 p-1 d-inline-grid">
                <div class="dropdown d-inline-grid">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Следующие
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach($next as $offset => $weekBorder)
                            <li><a class="dropdown-item" href="{{ route('schedule.index', ['week' => $offset]) }}">{{ $weekBorder }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
