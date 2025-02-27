<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('user.profile', compact('user'));
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
        $user->telegram = $request->telegram;
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
