<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\Course;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use function PHPUnit\Framework\isEmpty;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function index(Request $request)
    {
        $query = User::query()->where('username', '!=', 'admin')->with('roles')->orderByDesc('updated_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $role = $request->role;
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        $users = $query->paginate(10);

        return view('manage-user.index', compact('users'));
    }

    public function show($user_id)
    {
        $user = User::with('roles')->findOrFail($user_id);
        return view('manage-user.show')->with('user', $user);
    }

    public function update(UpdateUserRequest $request, $user_id)
    {
        $user = User::with('roles')->findOrFail($user_id);

        try {
            $this->userService->updateUser($user, $request->only(['name', 'username', 'email', 'role']));

            flash('User updated successfully')->success();
            return redirect()->route('manage-user.index');
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->back()->withInput();
        }
    }

    public function destroy($user_id)
    {
        $user = User::findOrFail($user_id);

        try {
            $this->userService->deleteUser($user);

            flash('User deleted successfully')->success();
            return redirect()->route('manage-user.index');
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->back();
        }
    }
}
