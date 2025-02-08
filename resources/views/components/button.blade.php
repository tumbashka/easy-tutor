@props([
    'color' => 'outline-light',
    'size' => 'xl',
])

<button {{ $attributes->class([
    "m-2 btn btn-{$color}",
    "btn-{$size}",
])->merge([
    'type' => 'button',
]) }}>
    {{ $slot }}
</button>
