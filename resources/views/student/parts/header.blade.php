<nav class="navbar sticky-top navbar-dark navbar-expand-xl bg-primary bg-gradient shadow mb-3">
    <div class="container">
        <a class="navbar-brand active mb-0 h1 text-white my-auto" href="{{ route('home') }}">
            {{ config('app.name') }}
            <i class="fa-solid fa-tractor fa-xl"></i>
        </a>

        <div class="time-widget d-xl-none text-white mx-2" id="timeWidgetMobile"></div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse container-fluid" id="navbarSupportedContent">
            @auth
                @if (auth()->user()->hasVerifiedEmail())
                    <ul class="nav nav-underline me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link text-white {{ activeLink('student.lessons.*') }}"
                               href="{{ route('student.lessons.index') }}">Главная</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ activeLink('student.boards.*') }}"
                               href="{{ route('student.boards.index') }}">Доски</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ activeLink('student.homework.*') }}"
                               href="{{ route('student.homework.index') }}">ДЗ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ activeLink('student.teachers.*') }}"
                               href="{{ route('student.teachers.index') }}">Преподаватели</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ activeLink('student.messages.*') }}"
                               href="{{ route('student.messages.index') }}">Сообщения</a>
                        </li>
                        <div class="time-widget d-none d-xl-flex text-white mx-3 align-self-center"
                             id="timeWidgetDesktop"></div>
                    </ul>
                @endif
            @endauth
            @guest
                <ul class="nav nav-underline me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-white {{ activeLink('login*') }}" href="{{ route('login') }}">
                            Вход
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ activeLink('register*') }}"
                           href="{{ route('register') }}">
                            Регистрация
                        </a>
                    </li>
                </ul>
            @endguest
            @auth
                <ul class="nav nav-underline mb-2 mb-lg-0 align-items-center">
                    <x-notifications.notifications-dropdown/>
                    <li class="nav-item dropdown-center">
                        <a class="nav-link link-light dropdown-toggle {{ activeLink('user*') }}"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            @can('active-account')
                                <li><a class="dropdown-item" href="{{ route('student.profile.index') }}">@lang('Профиль')</a></li>
                                <li><a class="dropdown-item" href="{{ route('student.settings.index') }}">@lang('Настройки')</a></li>
                            @endcan
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Выйти</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</nav>

@pushonce('js')

    @auth
        @if (auth()->user()->hasVerifiedEmail() && auth()->user()->is_active)
            <script>
                const lessons = {!! auth()->user()->getTodayActualLessons()->toJson() !!};
            </script>
        @endif
    @endauth

    <script>
        function formatTime(date) {
            return date.toTimeString().slice(0, 8).replace(/:/g, '<span class="colon">:</span>');
        }

        function formatLessonTime(time) {
            return time.slice(0, 5);
        }

        function calculateTimeDiff(start, end) {
            const diffMs = end - start;
            const hours = Math.floor(diffMs / (1000 * 60 * 60));
            const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
            if (hours > 0) {
                return `${hours} ч. ${minutes} мин.`;
            } else {
                return `${minutes} мин.`;
            }
        }

        let lastCurrentLessonId = null;
        let lastNextLessonId = null;
        let isInitialLoad = true;

        function updateWidget(isLessonChange = false) {
            const now = new Date();
            const mobileWidget = document.getElementById('timeWidgetMobile');
            const desktopWidget = document.getElementById('timeWidgetDesktop');

            if (!lessons || lessons.length === 0) {
                mobileWidget.innerHTML = `<div class="lesson-text${isLessonChange && !isInitialLoad ? ' animate' : ''}">Уроков нет</div>`;
                desktopWidget.innerHTML = `
                <div class="time-container">
                    <div class="current-time">${formatTime(now)}</div>
                    <div class="lessons-container"><div class="lesson-text${isLessonChange && !isInitialLoad ? ' animate' : ''}">Уроков нет</div></div>
                </div>`;
                lastCurrentLessonId = null;
                lastNextLessonId = null;
                isInitialLoad = false;
                return;
            }

            const activeLessons = lessons.filter(lesson => !lesson.is_canceled);
            const currentLesson = activeLessons.find(lesson => {
                const start = new Date(`${lesson.date}T${lesson.start}:00`);
                const end = new Date(`${lesson.date}T${lesson.end}:00`);
                return now >= start && now <= end;
            });

            const nextLesson = activeLessons
                .filter(lesson => new Date(`${lesson.date}T${lesson.start}:00`) > now)
                .sort((a, b) => new Date(`${a.date}T${a.start}:00`) - new Date(`${b.date}T${b.start}:00`))[0];

            const currentLessonId = currentLesson ? currentLesson.id : null;
            const nextLessonId = nextLesson ? nextLesson.id : null;
            const hasLessonChanged = currentLessonId !== lastCurrentLessonId || nextLessonId !== lastNextLessonId;

            let mobileCurrentText = currentLesson
                ? `${formatLessonTime(currentLesson.start)} - ${currentLesson.student_name}`
                : 'Нет текущего урока';
            let mobileNextText = nextLesson
                ? `${formatLessonTime(nextLesson.start)} - ${nextLesson.student_name}`
                : 'Нет след. урока';

            let desktopCurrentText = currentLesson
                ? `${formatLessonTime(currentLesson.start)} - ${currentLesson.student_name} (осталось ${calculateTimeDiff(now, new Date(`${currentLesson.date}T${currentLesson.end}:00`))})`
                : 'Нет текущего урока';
            let desktopNextText = nextLesson
                ? `${formatLessonTime(nextLesson.start)} - ${nextLesson.student_name} (через ${calculateTimeDiff(now, new Date(`${nextLesson.date}T${nextLesson.start}:00`))})`
                : 'Нет следующего урока';

            const animateClass = (isLessonChange || hasLessonChanged) && !isInitialLoad ? ' animate' : '';

            mobileWidget.innerHTML = `
            <div class="lesson-text${animateClass}">${mobileCurrentText}</div>
            <div class="lesson-text${animateClass}">${mobileNextText}</div>
        `;

            desktopWidget.innerHTML = `
            <div class="time-container">
                <div class="current-time">${formatTime(now)}</div>
                <div class="lessons-container">
                    <div class="lesson-text${animateClass}">${desktopCurrentText}</div>
                    <div class="lesson-text${animateClass}">${desktopNextText}</div>
                </div>
            </div>
        `;

            lastCurrentLessonId = currentLessonId;
            lastNextLessonId = nextLessonId;
            isInitialLoad = false;
        }

        setInterval(() => {
            const now = new Date();
            document.querySelectorAll('.current-time').forEach(el => {
                el.innerHTML = formatTime(now);
            });
        }, 1000);

        function scheduleMinuteUpdate() {
            const now = new Date();
            const msUntilNextMinute = (60 - now.getSeconds()) * 1000 - now.getMilliseconds();
            setTimeout(() => {
                updateWidget(false);
                setInterval(() => updateWidget(false), 60000);
            }, msUntilNextMinute);
        }

        function scheduleLessonUpdate() {
            const now = new Date();
            const activeLessons = lessons ? lessons.filter(lesson => !lesson.is_canceled) : [];
            let nextUpdateTime = null;

            activeLessons.forEach(lesson => {
                const start = new Date(`${lesson.date}T${lesson.start}:00`);
                const end = new Date(`${lesson.date}T${lesson.end}:00`);
                if (start > now && (!nextUpdateTime || start < nextUpdateTime)) {
                    nextUpdateTime = start;
                }
                if (end > now && (!nextUpdateTime || end < nextUpdateTime)) {
                    nextUpdateTime = end;
                }
            });

            if (nextUpdateTime) {
                const msUntilNextUpdate = nextUpdateTime - now;
                setTimeout(() => {
                    updateWidget(true);
                    scheduleLessonUpdate();
                }, msUntilNextUpdate);
            } else {
                setTimeout(scheduleLessonUpdate, 3600000);
            }
        }

        updateWidget(false);
        scheduleMinuteUpdate();
        scheduleLessonUpdate();
    </script>

    <style>
        .time-widget {
            font-size: 0.9rem;
            line-height: 1.2;
            text-align: center;
        }

        .time-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .current-time {
            font-size: 1.7rem;
            font-weight: bold;
        }

        .colon {
            animation: blink 1.5s ease-in-out infinite;
        }

        .lessons-container {
            text-align: left;
        }

        .time-widget div {
            white-space: nowrap;
        }

        @media (max-width: 991px) {
            .time-widget {
                flex-grow: 1;
            }
        }

        @media (min-width: 992px) {
            .time-widget {
                flex-shrink: 0;
            }
        }

        @keyframes blink {
            0%, 50% {
                opacity: 1;
            }
            51%, 100% {
                opacity: 0.8;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Скрываем часы только между 992px и 1399.98px */
        @media (min-width: 992px) and (max-width: 1399.98px) {
            .current-time {
                display: none !important;
            }
        }
    </style>
@endpushonce
