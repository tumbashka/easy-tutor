@props([
    'user' => null,
])
<div class="row g-0">
    <div class="col-md-4 text-center align-content-center">
        <x-user.avatar :avatar_url="$user->avatar_url"/>
        <h4>{{ $user->name }}</h4>
    </div>
    <div class="col-md-8">
        <div class="card-body p-4 text-center">
            <div class="row pt-1 ">
                <div class="col-auto">
                    <h6>Дата регистрации:</h6>
                </div>
                <div class="col-auto">
                    <p class="text-muted">{{ (new \Illuminate\Support\Carbon($user->created_at))->translatedFormat('d F Yг.') }}</p>
                </div>
            </div>
            <div class="row pt-1">
                <div class="col-auto">
                    <h6>Проведено занятий:</h6>
                </div>
                <div class="col-auto">
                    <p class="text-muted">{{$user->count_past_lessons }}</p>
                </div>
            </div>
            <h6>О себе</h6>
            <hr class="mt-0 mb-4">
            <div class="row pt-1">
                <p class="text-muted text-start">{{ $user->about }}</p>
            </div>
            <hr class="mt-0 mb-4">
            <div class="row pt-1">
                @if($user->telegram_username)
                    <div class="col-sm mb-3 mb-sm-0">
                        <a style="color: #27a7e7" target="_blank" href="https://t.me/{{$user->telegram_username}}"
                           class="link-underline link-underline-opacity-0">
                            <i class="fa-brands fa-telegram fa-xl"></i>
                            {{ $user->telegram_username }}
                        </a>
                    </div>
                @endif
                @if($user->phone)
                    <div class="col-sm">
                        <a href="tel:{{$user->phone}}" class="link-underline link-underline-opacity-0">
                            <i class="fa-solid fa-phone fa-lg"></i>
                            {{ $user->phone }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>





