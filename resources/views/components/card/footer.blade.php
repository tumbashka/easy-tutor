@props([
    'bg_color' => 'bg-primary',
])
<div class="card-footer {{ $bg_color }} bg-gradient d-grid">
    {{ $slot }}
</div>
