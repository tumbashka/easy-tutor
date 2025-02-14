@props([
    'title' => '',
    'left_text' => '',
    'left_url' => '',
    'right_text' => '',
    'right_url' => '',
    ])
<div class="row justify-content-center mx-0 pb-3">
    <div class="shadow  p-2 m-0 bg-info bg-gradient rounded text-center col-12 col-md-6 col-lg-5 col-xl-4 col-xxl-3">
        <div class="row pb-2">
            <div class="col">
                <h6 class="text-white m-0">
                    {{ $title }}
                </h6>
            </div>
        </div>
        <div class="row">
            <div class="col pe-1">
                <x-link-button class="btn-sm" href="{{ $left_url }}">
                    {{ $left_text }}
                </x-link-button>
            </div>
            <div class="col ps-1">
                <x-link-button class="btn-sm" href="{{ $right_url }}">
                    {{ $right_text }}
                </x-link-button>
            </div>

        </div>
    </div>
</div>
