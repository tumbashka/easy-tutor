@props([
    'user' => null,
])
<div class="row g-0">
    <div class="col-md-4 text-center align-content-center">
        @if($user)
            <x-user.avatar :avatar_url="$user->avatar_url"/>
        @endif
        <div class="form-group">
            <label for="avatar">Аватар</label>
            <input type="file" class="form-control form-control-sm" id="avatar" name="avatar">
            @error('avatar')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-8">
        <div class="card-body p-4 text-center">
            <div class="row pt-1 ">
                <div class="d-flex flex-row align-items-center ps-5">
                    <x-form.input-error-alert :name="'name'"/>
                </div>
                <x-form.input-float
                    :value="$user->name ?? ''"
                    :text="'Имя'"
                    :name="'name'"/>
                <x-form.input-float
                    :value="$user->email  ?? ''"
                    :type="'email'"
                    :text="'Email'"
                    :name="'email'"/>
                <x-form.input-float
                    :type="'password'"
                    :text="'Пароль'"
                    :name="'password'"/>
                <x-form.input-float
                    :type="'password'"
                    :text="'Подтвердите пароль'"
                    :name="'password_confirmation'"/>
            </div>
            <x-form.input-error-alert :name="'is_admin'"/>
            <div class="d-flex border rounded py-2 px-3 mb-4">
                <label class="me-3">Администратор:</label>
                <div class="form-check form-switch align-items-center m-0">
                    <input class="form-check-input" type="checkbox" {{ $user != null && $user->is_admin ? 'checked' : '' }} value="1" role="switch" name="is_admin">
                </div>
            </div>
            <x-form.input-error-alert :name="'is_active'"/>
            <div class="d-flex border rounded py-2 px-3 mb-4">
                <label class="me-3">Активен:</label>
                <div class="form-check form-switch align-items-center m-0">
                    <input class="form-check-input" type="checkbox" {{ $user != null && $user->is_active ? 'checked' : '' }} value="1" role="switch" name="is_active">
                </div>
            </div>
            <x-form.input-error-alert :name="'is_verify_email'"/>
            <div class="d-flex border rounded py-2 px-3 mb-4">
                <label class="me-3">Email подтверждён:</label>
                <div class="form-check form-switch align-items-center m-0">
                    <input class="form-check-input" type="checkbox" {{ $user != null && $user->email_verified_at ? 'checked' : '' }}  value="1" role="switch" name="is_verify_email">
                </div>
            </div>
            <x-form.input-error-alert :name="'about'"/>
            <div class="form-floating">
                <textarea class="form-control mb-4" style="height: 200px"
                          name="about">{{ old('about') ?? $user->about ?? '' }}</textarea>
                <label>О себе</label>
            </div>
            <x-form.input-error-alert :name="'phone'"/>
            <div class="d-flex flex-row align-items-center mb-4">
                <div class="form-floating flex-fill mb-0">
                    <input type="tel" id="phone" name="phone"
                           value="{{ old('phone') ?? $user->phone ?? '' }}"
                           class="phone-mask form-control"
                           placeholder="+7 (___) ___-__-__"
                           pattern="^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$">
                    <label class="form-label">Номер телефона</label>
                </div>
            </div>
            <x-form.input-error-alert :name="'telegram'"/>
            <div class="d-flex flex-row align-items-center">
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">@</span>
                    <input type="text" class="form-control"
                           placeholder="Ник в Telegram"
                           aria-label="Username"
                           aria-describedby="basic-addon1"
                           name="telegram"
                           value="{{ old('telegram') ?? $user->telegram ?? '' }}">
                </div>
            </div>
        </div>
    </div>
</div>





