@props([
    'title' => '',
    'bg_color' => 'bg-primary',
    'text_color' => 'text-white',
    'title_size' => 'h5'
    ])
<div class="card-header {{ $bg_color }} bg-gradient">
    <div class="row justify-content-center mx-0">
        <div class="m-0 rounded-3 text-center">
            <div class="row">
                <div class="col">
                    <div class="{{ $text_color }} m-0 d-inline {{ $title_size }}">
                        {{ $title }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
