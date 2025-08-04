<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\User\UpdateUserRequest;
use App\Models\User;
use App\Services\ImageService;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->updateConnectToTelegramToken();
        $telegram_connect_url = $user->connect_to_telegram_url;

        $bot_username = config('telegram.bots.mybot.username');
        $telegram_bot_url = "https://t.me/{$bot_username}";

        return view('teacher.profile.index', compact('user', 'telegram_connect_url', 'telegram_bot_url'));
    }

    public function edit()
    {
        if ($user = auth()->user()) {
            return view('teacher.profile.edit', compact('user'));
        } else {
            abort(403);
        }
    }

    public function update(UpdateUserRequest $request)
    {
        $user = auth()->user();
        if ($request->hasFile('avatar')) {
            $imageService = app()->make(ImageService::class);
            $imageService->uploadAvatar($user, $request->file('avatar'));
        }

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->name = $request->name;
        $user->about = $request->about;
        $user->phone = $request->phone;

        if ($user->save()) {
            session(['success' => 'Изменения успешно сохранены!']);
        } else {
            session(['error' => 'Ошибка сохранения изменений!']);
        }

        return redirect()->route('user.index');
    }

    public function show(User $user)
    {
        if (! $user->is_active) {
            abort(404);
        }

        return view('teacher.profile.index', compact('user'));
    }
}
