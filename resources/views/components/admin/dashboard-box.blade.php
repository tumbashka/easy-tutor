@props([
    'text_color' => 'text-light',
    'value' => 123,
    'label' => 'Label!',
    'link_label' => 'Подробнее',
    'link_url' => '',
    'color' => '#53C070',
    'icon' => 'fa-alien',
])
<div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xxl-3 mb-3">
    <div class="small-box" style="background-color: {{$color}}">
        <div class="inner {{ getTextContrastColor($color) }}">
            <h3>{{ $value }}</h3>
            <p>{{ $label }}</p>
        </div>
        <div class="icon">
            <i class="fas fa-regular {{ $icon }} fa-5x"></i>
        </div>
        <a href="{{ $link_url }}" class="small-box-footer {{ getTextContrastColor($color) }}">{{ $link_label }} <i class="fas fa-arrow-circle-right"></i></a>
    </div>
</div>
