<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles;
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
            'admin' => User::where('role', Roles::Admin)->orderBy('created_at', 'desc')->paginate(),
            default => User::orderBy('created_at', 'desc')->paginate(10),
        };

        return view('admin.user.index', compact('users', 'filter'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $roles = Roles::cases();

        return view('admin.user.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $user = User::forceCreate([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'about' => $request->input('about'),
            'phone' => $request->input('phone'),
            'telegram_username' => $request->input('telegram_username'),
            'telegram_id' => $request->input('telegram_id'),
            'role' => $request->input('role'),
            'is_active' => $request->input('is_active'),
            'email_verified_at' => $request->input('is_verify_email') ? now() : null,
        ]);

        if ($request->hasFile('avatar')) {
            app(ImageService::class)->uploadAvatar($user, $request->file('avatar'));
        }

        if ($user) {
            session(['success' => 'Пользователь успешно добавлен!']);
        } else {
            session(['error' => 'Ошибка добавления пользователя!']);
        }

        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        $roles = Roles::cases();

        return view('admin.user.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->is_active = (bool) $request->input('is_active');
        $user->email_verified_at = $request->input('is_verify_email') ? now() : null;
        $user->about = $request->input('about');
        $user->phone = $request->input('phone');
        $user->telegram_username = $request->input('telegram_username');
        $user->telegram_id = $request->input('telegram_id');
        $user->role = $request->input('role');

        if ($request->hasFile('avatar')) {
            app(ImageService::class)->uploadAvatar($user, $request->file('avatar'));
        }

        if ($request->input('password')) {
            $user->password = $request->input('password');
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
        $this->authorize('delete', $user);

        if ($user->delete()) {
            session(['success' => 'Пользователь успешно удалён!']);
        } else {
            session(['error' => 'Ошибка удаления пользователя!']);
        }

        return redirect()->route('admin.users.index');
    }
}
