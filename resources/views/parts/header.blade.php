<nav class="navbar navbar-expand-md bg-info bg-gradient shadow mb-3">
    <div class="container ">
        <a class="navbar-brand mb-0 h1 text-white" href="{{ route('home') }}">
            {{ config('app.name') }}
            {{--            <i class="fa-duotone fa-solid fa-tractor"></i>--}}
            <i class="fa-regular fa-book-open-cover fa-lg"></i>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse container-fluid" id="navbarSupportedContent">
            @auth
                @if (auth()->user()->hasVerifiedEmail())
                    <ul class="nav nav-underline me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link text-white {{ activeLink('schedule.*') }}"
                               href="{{ route('home') }}">Главная</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ activeLink('student*') }}"
                               href="{{ route('student.index') }}">Ученики</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ activeLink('homework*') }}" href="#">Домашняя работа</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ activeLink('homework*') }}" href="#">Свободное время</a>
                        </li>
                    </ul>
                @endif
            @endauth
            @guest
                <ul class="nav nav-underline me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-white {{ activeLink('login.*') }}" href="{{ route('login') }}">
                            Вход
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ activeLink('registration*') }}"
                           href="{{ route('registration') }}">
                            Регистрация
                        </a>
                    </li>
                </ul>
            @endguest
            @auth
                <ul class="justify-content-end nav nav-underline mb-2 mb-lg-0">
                    @if (auth()->user()->hasVerifiedEmail())
                        <li class="nav-item dropdown-center">
                            <a class="nav-link link-light dropdown-toggle {{ activeLink('user*') }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><h6 class="dropdown-header">Пользователь</h6></li>
                                <li><hr class="dropdown-divider m-0"></li>
                                <li><a class="dropdown-item" href="#">Профиль</a></li>
                                <li><a class="dropdown-item" href="#">Настройки</a></li>
                                <li><hr class="dropdown-divider m-0"></li>
                                <li><h6 class="dropdown-header">Статистика</h6></li>
                                <li><hr class="dropdown-divider m-0"></li>
                                <li><a class="dropdown-item" href="#">Заработок</a></li>
                                <li><a class="dropdown-item" href="#">Занятия</a></li>
                                <li><a class="dropdown-item" href="#">Ученики</a></li>
                            </ul>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('logout') }}">
                            Выйти
                        </a>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</nav>
