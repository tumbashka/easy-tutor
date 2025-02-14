@props([
    'href' => '',
])
<div class="shadow d-grid bg-info bg-gradient rounded-2 border mb-3 ">
    <a class="d-grid text-decoration-none mx-3 my-2" href="{{ $href }}">
        <x-button {{ $attributes }}>
            {{ $slot }}
        </x-button>
    </a>
</div>


