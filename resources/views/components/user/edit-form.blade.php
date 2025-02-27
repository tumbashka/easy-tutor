@props([
    'user' => null,
])
<div class="row g-0">
    <div class="col-md-4 text-center align-content-center">
        <x-user.avatar :avatar_url="$user->avatar_url"/>
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
                <div class="d-flex flex-row align-items-center mb-4">
                    <div class="form-floating flex-fill mb-0">
                        <input type="text" class="form-control
                                {{ $errors->has('name') ? 'is-invalid' : '' }}"
                               value="{{ old('name') ?? $user->name ?? '' }}"
                               placeholder="Имя" name="name"/>
                        <label class="form-label">Имя</label>
                    </div>
                </div>
                <x-form.input-float
                    :type="'password'"
                    :text="'Новый пароль'"
                    :name="'password'"/>
                <x-form.input-float
                    :type="'password'"
                    :text="'Подтвердите пароль'"
                    :name="'password_confirmation'"/>
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
            <div class="d-flex flex-row align-items-center mb-4">
                <div class="form-floating flex-fill mb-0">
                    <input type="text" name="telegram"
                           value="{{ old('telegram') ?? $user->telegram ?? '' }}"
                           class="form-control" placeholder="Ник в Telegram">
                    <label class="form-label">Ник в Telegram</label>
                </div>
            </div>
        </div>
    </div>
</div>





