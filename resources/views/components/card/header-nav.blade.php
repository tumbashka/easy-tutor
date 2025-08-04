@props([
    'title' => '',
    'text' => '',
    'url' => '',
    ])
<div class="card-header bg-primary bg-gradient">
    <div class="row justify-content-center mx-0">
        <div class="m-0 rounded-3 text-center">
            <div class="row">
                <div class="col">
                    <h5 class="text-white m-0 mb-1">
                        {{ $title }}
                    </h5>
                </div>
            </div>
            <div class="row">
                <div class="col rounded-3 m-0 p-0 d-grid">
                    <x-link-button href="{{ $url }}">
                        {{ $text }}
                    </x-link-button>
                </div>
            </div>
            <div class="row">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
