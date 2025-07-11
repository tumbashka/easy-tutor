@props([
    'student' => null,
    'url' => '',
])
<form method="post" action="{{ route('free-time.encrypt-url') }}">
    @csrf
    <div class="row align-items-center">
        <div class="col-sm-4">
            <p class="mb-0">Время действия ссылки:</p>
        </div>
        <div class="col-sm-8">
            <x-form.input-error-alert :name="'expire_time'"/>
            <select name="expire_time" required class="form-select {{ $errors->has('class') ? 'is-invalid' : '' }}">
                <option selected value="1">1 день</option>
                <option value="2">2 дня</option>
                <option value="4">4 дня</option>
                <option value="7">1 неделя</option>
                <option value="14">2 недели</option>
                <option value="31">1 месяц</option>
                <option value="62">2 месяца</option>
            </select>
        </div>
    </div>
    <hr>
    <div class="row align-items-center">
        <div class="col-4">
            <p class="mb-0">Показывать занятия:</p>
        </div>
        <div class="col-8 form-check form-switch">
            <x-form.input-error-alert :name="'allow_lessons'"/>
            <input class="form-check-input ms-1" style="min-height: 20px; min-width: 40px" type="checkbox" name="allow_lessons" value="1" id="flexSwitchCheckDefault">
        </div>
    </div>
    <hr>
    <div class="row align-items-center">
        <div class="col-sm-4">
            <p class="mb-0">Ссылка на окна:</p>
        </div>
        <div class="col-sm-8">
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="encrypted_url"
                       value="{{ $url }}" readonly>
                <button type="button" class="btn btn-outline-info btn-copy" data-bs-toggle="tooltip"
                        data-clipboard-target="#encrypted_url">
                    <i class="far fa-copy"></i>
                </button>
            </div>
            <div class="shadow d-grid bg-primary bg-gradient rounded-2 border mb-1 px-1">
                <x-button class="my-1" type="submit" :size="'sm'">
                    Сгенерировать ссылку на расписание
                </x-button>
            </div>
        </div>
    </div>
</form>
