<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'filter' => ['nullable', 'in:active,not_active,admin'],
        ]);
        $filter = null;
        if (isset($validated['filter'])) {
            $filter = $validated['filter'];
        }

        $users = match ($filter) {
            'active' => User::where('is_active', 1)->orderBy('created_at', 'desc')->paginate(),
            'not_active' => User::where('is_active', 0)->orderBy('created_at', 'desc')->paginate(),
            'admin' => User::where('is_admin', 1)->orderBy('created_at', 'desc')->paginate(),
            default => User::orderBy('created_at', 'desc')->paginate(),
        };

        return view('admin.user.index', compact('users', 'filter'));
    }

    public function create()
    {
        return view('admin.user.create');
    }

    public function store(StoreUserRequest $request)
    {
        if (auth()->user()->cant('create', User::class)) {
            abort(403);
        }
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'about' => $request->input('about'),
            'phone' => $request->input('phone'),
            'telegram_username' => $request->input('telegram'),
            'telegram_id' => $request->input('telegram_id'),
        ]);

        if ($request->hasFile('avatar')) {
            app(ImageService::class)->uploadAvatar($user, $request->file('avatar'));
        }

        if ($request->input('is_verify_email')) {
            $user->email_verified_at = now();
        }

        $user->is_admin = (bool) $request->input('is_admin');
        $user->is_active = (bool) $request->input('is_active');

        if ($user->update()) {
            session(['success' => 'Пользователь успешно добавлен!']);
        } else {
            session(['error' => 'Ошибка добавления пользователя!']);
        }

        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        return view('admin.user.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {

        if (auth()->user()->cant('update', $user)) {
            abort(403);
        }
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->is_admin = (bool) $request->input('is_admin');
        $user->is_active = (bool) $request->input('is_active');
        $user->about = $request->input('about');
        $user->phone = $request->input('phone');
        $user->telegram_username = $request->input('telegram_username');
        $user->telegram_id = $request->input('telegram_id');
        $user->update();

        if ($request->hasFile('avatar')) {
            app(ImageService::class)->uploadAvatar($user, $request->file('avatar'));
        }

        if ($request->input('password')) {
            $user->password = $request->input('password');
        }

        if ($request->input('is_verify_email')) {
            $user->email_verified_at = now();
        } else {
            $user->email_verified_at = null;
        }

        if ($user->update()) {
            session(['success' => 'Пользователь успешно отредактирован!']);
        } else {
            session(['error' => 'Ошибка редактирования пользователя!']);
        }

        return redirect()->route('admin.users.index');
    }

    public function destroy(User $user)
    {
        if (auth()->user()->cant('delete', $user)) {
            abort(403);
        }

        if ($user->delete()) {
            session(['success' => 'Пользователь успешно удалён!']);
        } else {
            session(['error' => 'Ошибка удаления пользователя!']);
        }

        return redirect()->route('admin.users.index');
    }
}
