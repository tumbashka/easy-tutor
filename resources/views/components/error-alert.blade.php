@if($errors->any())
    <div class="alert alert-danger m-2 mb-0" role="alert">
        <ul class="ms-4 mb-0">
            @foreach($errors->all() as $error)
                <li class="mb-0">
                    {{$error}}
                </li>
            @endforeach
        </ul>
    </div>
@endif
