@props([
    'second_col' => null,
])
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-7 col-xxl-6 px-2">
            {{ $slot }}
        </div>
        @if($second_col)
            <div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-7 col-xxl-6 px-2">
                {{ $second_col }}
            </div>
        @endif
    </div>
</div>
