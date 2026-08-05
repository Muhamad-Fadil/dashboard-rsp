<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\View\View;

class DivisiController extends Controller
{
    public function show(Division $division): View
    {
        return view('divisi.dashboard', compact('division'));
    }
}
