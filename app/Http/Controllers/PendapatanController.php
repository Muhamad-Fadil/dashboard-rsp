<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Pendapatan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PendapatanController extends Controller
{
    protected const TAB_VALID = ['semua', 'bpjs', 'tunai', 'igd', 'poliklinik', 'ruangan'];

    protected const TAB_BELUM_TERSEDIA = ['tunai', 'poliklinik', 'ruangan'];

    public function index(Request $request, Division $division)
    {
        [$awal, $akhir, $tab] = $this->ambilFilter($request);

        if (in_array($tab, self::TAB_BELUM_TERSEDIA, true)) {
            return view('divisi.keuangan.pendapatan', [
                'division' => $division,
                'tab' => $tab,
                'pendapatan' => collect(),
                'totalPendapatan' => 0,
                'awal' => $awal,
                'akhir' => $akhir,
            ]);
        }

        $pendapatan = $this->query($awal, $akhir, $tab)->latest('tanggal')->get();
        $totalPendapatan = $pendapatan->sum('jumlah');

        return view('divisi.keuangan.pendapatan', compact(
            'division', 'tab', 'pendapatan', 'totalPendapatan', 'awal', 'akhir'
        ));
    }

    public function exportPdf(Request $request, Division $division)
    {
        [$awal, $akhir, $tab] = $this->ambilFilter($request);

        $pendapatan = in_array($tab, self::TAB_BELUM_TERSEDIA, true)
            ? collect()
            : $this->query($awal, $akhir, $tab)->latest('tanggal')->get();

        $pdf = Pdf::loadView('pdf.keuangan.pendapatan', [
            'pendapatan' => $pendapatan,
            'awal' => $awal,
            'akhir' => $akhir,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('data-pendapatan.pdf');
    }

    private function ambilFilter(Request $request): array
    {
        $awal = $request->filled('awal')
            ? $request->awal
            : now()->startOfMonth()->toDateString();

        $akhir = $request->filled('akhir')
            ? $request->akhir
            : now()->toDateString();

        $tab = $request->query('tab', 'semua');

        if (! in_array($tab, self::TAB_VALID, true)) {
            $tab = 'semua';
        }

        return [$awal, $akhir, $tab];
    }

    private function query(string $awal, string $akhir, string $tab): Builder
    {
        $query = Pendapatan::with(['kategori', 'unitKerja'])
            ->whereBetween('tanggal', [$awal, $akhir]);

        return match ($tab) {
            'bpjs' => $query->whereHas('kategori', fn ($q) => $q->where('kode', 'PDT-07')),
            'igd' => $query->whereHas('unitKerja', fn ($q) => $q->where('kode_unit', 'IGD')),
            default => $query,
        };
    }
}