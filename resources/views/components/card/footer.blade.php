@props([
    'bg_color' => 'bg-info',
])
<div class="card-footer {{ $bg_color }} bg-gradient d-grid">
    {{ $slot }}
</div>
