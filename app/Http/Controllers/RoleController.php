<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Show the active roles.
     */
    public function index(): View
    {
        $roles = Role::orderBy('id')->paginate(10);

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Create a new role.
     */
    public function store(Request $request): RedirectResponse
    {
        Role::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]));

        return back()->with('success', 'Rol agregado correctamente');
    }

    /**
     * Rename the given role.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $role->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]));

        return back()->with('success', 'Rol editado correctamente');
    }

    /**
     * Soft delete the given role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return back()->with('success', 'Rol eliminado correctamente');
    }

    /**
     * Restore a soft deleted role.
     */
    public function restore(Role $role): RedirectResponse
    {
        $role->restore();

        return back()->with('success', 'Rol restaurado correctamente');
    }
}
