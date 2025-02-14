<nav class="navbar navbar-expand-lg bg-info bg-gradient shadow mb-3">
    <div class="container ">
        <a class="navbar-brand mb-0 h1 text-white my-auto" href="{{ route('home') }}">
            {{ config('app.name') }}
            <img src="/images/icons/book_white.svg" height="28px">
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
                            <a class="nav-link text-white {{ activeLink('free-time*') }}" href="{{ route('free-time.index') }}">Окна</a>
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
                <ul class="nav nav-underline mb-2 mb-lg-0">
                    @if (auth()->user()->hasVerifiedEmail())
                        <li class="nav-item dropdown-center">
                            <a class="nav-link link-light dropdown-toggle {{ activeLink('statistic*') }}" href="#"
                               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Статистика
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <hr class="dropdown-divider m-0">
                                </li>
                                <li><h6 class="dropdown-header">Доходы</h6></li>
                                <li><a class="dropdown-item" href="{{ route('statistic.earnings.period') }}">По
                                        периодам</a></li>
                                <li><a class="dropdown-item" href="{{ route('statistic.earnings.students') }}">По
                                        ученикам</a></li>
                                <li>
                                    <hr class="dropdown-divider m-0">
                                </li>
                                <li><h6 class="dropdown-header">Занятия</h6></li>
                                <li><a class="dropdown-item" href="{{ route('statistic.lessons.period') }}">По
                                        периодам</a></li>
                                <li><a class="dropdown-item" href="{{ route('statistic.lessons.students') }}">По
                                        ученикам</a></li>
                                <li>
                                    <hr class="dropdown-divider m-0">
                                </li>
                                <li><h6 class="dropdown-header">Рабочие часы</h6></li>
                                <li><a class="dropdown-item" href="{{ route('statistic.time.period') }}">По периодам</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown-center">
                            <a class="nav-link link-light dropdown-toggle {{ activeLink('user*') }}" href="#"
                               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Профиль</a></li>
                                <li><a class="dropdown-item" href="#">Настройки</a></li>
                                <li><a class="dropdown-item" href="{{ route('logout') }}">Выйти</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            @endauth
        </div>
    </div>
</nav>
