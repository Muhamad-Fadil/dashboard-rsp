<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $totalUsers = User::count();
        $divisions = Division::withCount('users')->get();

        $cari = $request->query('cari');
        $roleFilter = $request->query('role');

        $users = User::with('division')
            ->when($cari, function ($query, $cari) {
                $query->where(fn ($q) => $q->where('name', 'like', "%{$cari}%")->orWhere('email', 'like', "%{$cari}%"));
            })
            ->when($roleFilter, fn ($query, $roleFilter) => $query->where('role', $roleFilter))
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'divisions' => $divisions,
            'users' => $users,
            'cari' => $cari,
            'roleFilter' => $roleFilter,
        ]);
    }

    public function editUser(User $user): View
    {
        return view('admin.user-edit', [
            'targetUser' => $user,
            'divisions' => Division::all(),
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->route('admin.dashboard')->with('status', 'Akun "' . $user->name . '" berhasil diperbarui.');
    }
}