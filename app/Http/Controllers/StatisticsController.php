<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    /**
     * Show how many active users each role has.
     */
    public function index(): View
    {
        $roles = Role::withCount('users')->orderBy('id')->get(['id', 'name']);

        return view('admin.statistics', compact('roles'));
    }
}
