@props(['href' => ''])
<div class="d-grid">
    <a class="d-grid text-decoration-none" href="{{ $href }}">
        <x-button {{ $attributes }}>
            {{ $slot }}
        </x-button>
    </a>
</div>


