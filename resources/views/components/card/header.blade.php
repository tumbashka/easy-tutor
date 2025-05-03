@props([
    'title' => '',
    'bg_color' => 'bg-info',
    'text_color' => 'text-white',
    ])
<div class="card-header {{ $bg_color }} bg-gradient">
    <div class="row justify-content-center mx-0">
        <div class="m-0 rounded-3 text-center">
            <div class="row">
                <div class="col">
                    <h5 class="{{ $text_color }} m-0 d-inline">
                        {{ $title }}
                    </h5>
                </div>
            </div>
        </div>
    </div>
</div>
