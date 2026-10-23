<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;

class RecycleBinController extends Controller
{
    /**
     * Show the soft deleted users, categories and roles.
     */
    public function index(): View
    {
        $users = User::onlyTrashed()->with('role')->orderBy('id')->get();
        $categories = Category::onlyTrashed()->orderBy('id')->get();
        $roles = Role::onlyTrashed()->orderBy('id')->get();

        return view('admin.recycle-bin', compact('users', 'categories', 'roles'));
    }
}
