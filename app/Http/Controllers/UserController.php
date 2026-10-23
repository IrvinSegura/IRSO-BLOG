<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Show the active users, optionally filtered by name or email.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $users = User::with(['role', 'socialAccounts'])
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();
        $roles = Role::orderBy('id')->get();

        return view('admin.users.index', compact('users', 'roles', 'search'));
    }

    /**
     * Show the soft deleted users.
     */
    public function hidden(): View
    {
        $users = User::onlyTrashed()->with('role')->orderBy('id')->paginate(10);

        return view('admin.users.hidden', compact('users'));
    }

    /**
     * Create a new user.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'rol' => ['required', 'exists:role,id'],
        ]);

        // "rol" is not mass assignable, so it is filled explicitly.
        User::forceCreate($data);

        return back()->with('success', 'Usuario agregado correctamente');
    }

    /**
     * Update the name, email and role of the given user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'rol' => ['required', 'exists:role,id'],
        ]);

        $user->forceFill($data)->save();

        return back()->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Soft delete the given user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return back()->with('success', 'Usuario eliminado correctamente');
    }

    /**
     * Restore a soft deleted user.
     */
    public function restore(User $user): RedirectResponse
    {
        $user->restore();

        return back()->with('success', 'Usuario restaurado correctamente');
    }
}
