<div class="mb-1 d-flex justify-content-center text-center">
    <div class="col-md-8">
        <div class="">
            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#statisticsCollapse"
                    aria-expanded="false" aria-controls="statisticsCollapse">
                Показать статистику за неделю
            </button>
            <div class="collapse mt-2" id="statisticsCollapse">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-center">Статистика за неделю</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">Занятия</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Проведено
                                        <span
                                            class="badge bg-success rounded-pill">{{ $statistics['conductedLessons'] }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Идёт сейчас
                                        <span
                                            class="badge bg-warning rounded-pill">{{ $statistics['ongoingLessons'] }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Запланировано
                                        <span
                                            class="badge bg-info rounded-pill">{{ $statistics['toConductLessons'] }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Отменено
                                        <span
                                            class="badge bg-danger rounded-pill">{{ $statistics['canceledLessons'] }}</span>
                                    </li>
                                </ul>
                                @php
                                    $totalNonCanceled = $statistics['conductedLessons'] + $statistics['ongoingLessons'] + $statistics['toConductLessons'];
                                    $conductedPercent = $totalNonCanceled > 0 ? ($statistics['conductedLessons'] / $totalNonCanceled) * 100 : 0;
                                @endphp
                                <div class="progress mt-2" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: {{ $conductedPercent }}%;"
                                         aria-valuenow="{{ $statistics['conductedLessons'] }}" aria-valuemin="0"
                                         aria-valuemax="{{ $totalNonCanceled }}">
                                        {{ number_format($conductedPercent, 1) }}%
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">Заработок</h6>
                                <p>Заработано: <strong>{{ $statistics['earned'] }}</strong>
                                    из {{ $statistics['totalPossibleEarnings'] }}</p>
                                @php
                                    $earnedPercent = $statistics['totalPossibleEarnings'] > 0 ? ($statistics['earned'] / $statistics['totalPossibleEarnings']) * 100 : 0;
                                @endphp
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-info" role="progressbar"
                                         style="width: {{ $earnedPercent }}%;"
                                         aria-valuenow="{{ $statistics['earned'] }}" aria-valuemin="0"
                                         aria-valuemax="{{ $statistics['totalPossibleEarnings'] }}">
                                        {{ number_format($earnedPercent, 1) }}%
                                    </div>
                                </div>
                                <h6 class="text-muted mt-4">Часы</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        Проведено:
                                        <strong>{{ number_format($statistics['hoursConducted'], 1) }}</strong> часов
                                    </li>
                                    <li class="list-group-item">
                                        Запланировано:
                                        <strong>{{ number_format($statistics['hoursToConduct'], 1) }}</strong> часов
                                    </li>
                                </ul>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


