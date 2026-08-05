<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::count();
        $divisions = Division::withCount('users')->get();

        return view('admin.dashboard', compact('totalUsers', 'divisions'));
    }
}
