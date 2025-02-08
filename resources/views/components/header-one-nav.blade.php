@props([
    'title' => '',
    'text' => '',
    'url' => '',
    ])
<div class="row justify-content-center mx-0 pb-3">
    <div class="p-2 m-0 bg-light bg-gradient rounded-3 text-center col-12 col-md-6 col-lg-5 col-xl-4 col-xxl-3">
        <div class="row">
            <div class="col">
                <h6 class="text-white">
                    {{ $title }}
                </h6>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <x-link-button class="w-100" href="{{ $url }}">
                    {{ $text }}
                </x-link-button>
            </div>
        </div>
    </div>
</div>
