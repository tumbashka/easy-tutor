<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->generate_telegram_token();
        $telegram_connect_url = $user->get_telegram_url();

        $bot_username = config('telegram.bots.mybot.username');
        $telegram_bot_url = "https://t.me/{$bot_username}";

        return view('user.profile', compact('user', 'telegram_connect_url', 'telegram_bot_url'));
    }

    public function edit(User $user)
    {
        if (auth()->user()->id == $user->id) {
            return view('user.edit', compact('user'));
        } else {
            abort(403);
        }
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        if ($request->hasFile('avatar')) {
            $user->setAvatar($request->file('avatar'));
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

        return view('user.profile', compact('user'));
    }
}
