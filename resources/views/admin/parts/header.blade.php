<nav class="navbar navbar-dark navbar-expand-lg bg-primary bg-gradient shadow mb-3">
    <div class="container">
        <a class="navbar-brand m-0 h1 text-white my-auto align-items-center" href="{{ route('admin.dashboard') }}">
            Easy Admin
            <i class="fa-solid fa-screwdriver-wrench fa-xl"></i>
        </a>
        <button class="navbar-toggler  " type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse container-fluid" id="navbarSupportedContent">
            <ul class="nav nav-underline me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-white {{ activeLink('admin.dashboard*') }}"
                       href="{{ route('admin.dashboard') }}">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ activeLink('admin.users*') }}"
                       href="{{ route('admin.users.index') }}">Пользователи</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ activeLink('admin.backups*') }}"
                       href="{{ route('admin.backups') }}">Бэкапы</a>
                </li>
            </ul>
            <ul class="nav nav-underline mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-white"
                       href="{{ route('home') }}">
                        Панель педагога
                    </a>
                </li>
                <li class="nav-item dropdown-center">
                    <a class="nav-link link-light dropdown-toggle {{ activeLink('user*') }}" href="#"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('user.index') }}">Профиль</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Выйти</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
