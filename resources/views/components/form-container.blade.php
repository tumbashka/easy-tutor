@props([
    'second_col' => null,
    'second_col_classes' => [
        'col-12',
        'col-sm-12',
        'col-md-12',
        'col-lg-9',
        'col-xl-7',
        'col-xxl-5',
        'px-2',
    ],
])
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-7 col-xxl-7 px-2">
            {{ $slot }}
        </div>
        @if($second_col)
            <div class="{{ Arr::toCssClasses($second_col_classes) }}">
                {{ $second_col }}
            </div>
        @endif
    </div>
</div>
