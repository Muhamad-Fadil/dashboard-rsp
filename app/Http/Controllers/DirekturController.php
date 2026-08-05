<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\View\View;

class DirekturController extends Controller
{
    public function index(): View
    {
        $divisions = Division::all();

        return view('direktur.dashboard', compact('divisions'));
    }
}
