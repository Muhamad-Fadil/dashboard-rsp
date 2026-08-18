<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\OperatorSubmenuAkses;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class OperatorManagementController extends Controller
{
    public function index(Request $request): View
    {
        $auth = auth()->user();

        $query = User::where('role', 'operator')->with(['division', 'submenuAkses']);

        if ($auth->role === 'manajer') {
            $query->where('division_id', $auth->division_id);
        } elseif ($request->filled('division_id')) {
            $query->where('division_id', $request->query('division_id'));
        }

        $operators = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.operator.index', [
            'operators' => $operators,
            'divisions' => Division::all(),
            'filterDivisionId' => $request->query('division_id'),
        ]);
    }

    public function create(): View
    {
        $auth = auth()->user();

        // Manajer otomatis terkunci ke divisinya sendiri, Admin bebas pilih
        $division = $auth->role === 'manajer' ? $auth->division : null;

        return view('admin.operator.create', [
            'divisions' => Division::all(),
            'division' => $division,
            'daftarSubmenu' => User::daftarSubmenuLayanan(),
        ]);
    }

    public function store(Request $request)
    {
        $auth = auth()->user();

        $divisionId = $auth->role === 'manajer' ? $auth->division_id : $request->input('division_id');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'submenu' => ['array'],
            'submenu.*' => ['string'],
        ]);

        abort_unless($divisionId, 422, 'Divisi belum ditentukan.');

        $division = Division::findOrFail($divisionId);

        $operator = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 'operator',
            'division_id' => $division->id,
        ]);

        if ($division->slug === 'layanan') {
            foreach ($request->input('submenu', []) as $submenu) {
                OperatorSubmenuAkses::create([
                    'user_id' => $operator->id,
                    'submenu' => $submenu,
                ]);
            }
        }

        return redirect()->route('admin.operator.index')->with('status', 'Akun operator "' . $operator->name . '" berhasil dibuat.');
    }

    public function edit(User $operator): View
    {
        $this->pastikanBolehKelola($operator);

        return view('admin.operator.edit', [
            'operator' => $operator->load('submenuAkses'),
            'daftarSubmenu' => User::daftarSubmenuLayanan(),
            'aksesSaatIni' => $operator->submenuAkses->pluck('submenu')->all(),
        ]);
    }

    public function update(Request $request, User $operator)
    {
        $this->pastikanBolehKelola($operator);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $operator->id],
            'password' => ['nullable', 'string', 'min:6'],
            'submenu' => ['array'],
            'submenu.*' => ['string'],
        ]);

        $operator->name = $request->input('name');
        $operator->email = $request->input('email');

        if ($request->filled('password')) {
            $operator->password = Hash::make($request->input('password'));
        }

        $operator->save();

        if ($operator->division->slug === 'layanan') {
            // sinkronisasi: hapus semua akses lama, buat ulang sesuai centangan baru
            OperatorSubmenuAkses::where('user_id', $operator->id)->delete();

            foreach ($request->input('submenu', []) as $submenu) {
                OperatorSubmenuAkses::create([
                    'user_id' => $operator->id,
                    'submenu' => $submenu,
                ]);
            }
        }

        return redirect()->route('admin.operator.index')->with('status', 'Akun operator "' . $operator->name . '" berhasil diperbarui.');
    }

    public function destroy(User $operator)
    {
        $this->pastikanBolehKelola($operator);

        $namaOperator = $operator->name;
        $operator->delete(); // OperatorSubmenuAkses ikut kehapus otomatis (cascadeOnDelete)

        return redirect()->route('admin.operator.index')->with('status', 'Akun operator "' . $namaOperator . '" berhasil dihapus.');
    }

    /**
     * Manajer cuma boleh kelola operator di divisinya sendiri. Admin boleh kelola semua.
     */
    protected function pastikanBolehKelola(User $operator): void
    {
        abort_unless($operator->role === 'operator', 404);

        $auth = auth()->user();

        if ($auth->role === 'manajer' && $operator->division_id !== $auth->division_id) {
            abort(403, 'Anda tidak punya akses untuk mengelola operator divisi lain.');
        }
    }
}