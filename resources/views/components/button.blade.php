@props([
    'color' => 'outline-light',
    'size' => 'xl',
])

<button {{ $attributes->class([
    "btn btn-{$color}",
    "btn-{$size}",
])->merge([
    'type' => 'button',
]) }}>
    {{ $slot }}
</button>
