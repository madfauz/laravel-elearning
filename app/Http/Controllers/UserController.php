<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use function PHPUnit\Framework\isEmpty;

class UserController extends Controller
{
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

    public function update(Request $request, $user_id)
    {
        $user = User::with('roles')->find($user_id);

        if (!$user) {
            flash('User is not found')->error('');
            return redirect()->back();
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_num', Rule::unique('users')->ignore($user->user_id, "user_id")],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->user_id, 'user_id')],
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ]);

        if (!$user->hasRole($request->role)) {
            $user->syncRoles([$request->role]);
        }

        flash('User updated successfully')->success();
        return redirect()->route('manage-user.index');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        flash('User deleted successfully')->success();
        return redirect()->route('manage-user.index');
    }
}
