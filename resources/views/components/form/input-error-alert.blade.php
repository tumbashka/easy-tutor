@props(['name' => ''])

@if($errors->has($name))
    <div class="mb-0 text-danger">
        <ul class="mb-1">
            @foreach($errors->get($name) as $message)
                <li class="mb-0">
                    {{$message}}
                </li>
            @endforeach
        </ul>
    </div>
@endif
