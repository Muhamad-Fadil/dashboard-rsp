<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Pendapatan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PendapatanController extends Controller
{
    /**
     * Daftar tab pada halaman Pendapatan.
     * tipe 'kategori'    -> filter berdasarkan kode di tabel kategori_pendapatan
     * tipe 'pembayaran'  -> filter berdasarkan kolom jenis_pembayaran (bisa lebih dari 1 nilai, pakai LIKE)
     */
    private function daftarTab(): array
    {
        return [
           'semua'       => ['label' => 'Semua Kategori',          'tipe' => null,         'nilai' => null],
            'tunai'       => ['label' => 'Pembayaran Tunai',        'tipe' => 'pembayaran', 'nilai' => 'TUNAI'],
            'bpjs'        => ['label' => 'Pembayaran BPJS',         'tipe' => 'pembayaran', 'nilai' => 'BPJS'],
            'rawat-jalan' => ['label' => 'Rawat Jalan',             'tipe' => 'kategori',   'nilai' => 'PDT-01'],
            'rawat-inap'  => ['label' => 'Rawat Inap',              'tipe' => 'kategori',   'nilai' => 'PDT-02'],
            'igd'         => ['label' => 'Gawat Darurat (IGD)',     'tipe' => 'kategori',   'nilai' => 'PDT-08'],
        ];
    }

    private function tabBelumTersedia(): array
    {
        return [];
    }

    private function defaultAwal(): string
    {
        return Pendapatan::min('tanggal') ?? now()->startOfMonth()->toDateString();
    }

    private function defaultAkhir(): string
    {
        return Pendapatan::max('tanggal') ?? now()->toDateString();
    }

    private function queryPendapatan(string $tab, string $awal, string $akhir)
    {
        $daftarTab = $this->daftarTab();
        $konfigTab = $daftarTab[$tab] ?? null;

        $query = Pendapatan::with(['kategori', 'unitKerja'])
            ->whereBetween('tanggal', [$awal, $akhir]);

        if ($konfigTab) {
            if ($konfigTab['tipe'] === 'kategori') {
                $query->whereHas('kategori', fn ($q) => $q->where('kode', $konfigTab['nilai']));
            } elseif ($konfigTab['tipe'] === 'pembayaran') {
                $query->where('jenis_pembayaran', 'like', $konfigTab['nilai'].'%');
            }
        }

        return $query->latest('tanggal');
    }

    public function index(Request $request, Division $division)
    {
        $daftarTab = $this->daftarTab();

        $awal = $request->filled('awal') ? $request->awal : $this->defaultAwal();
        $akhir = $request->filled('akhir') ? $request->akhir : $this->defaultAkhir();
        $tab = $request->query('tab', array_key_first($daftarTab));

        if (! array_key_exists($tab, $daftarTab)) {
            $tab = array_key_first($daftarTab);
        }

        $pendapatan = $this->queryPendapatan($tab, $awal, $akhir)->get();
        $totalPendapatan = $pendapatan->sum('jumlah');

        $tabList = collect($daftarTab)->map(fn ($t) => $t['label'])->all();
        $tabBelumTersedia = $this->tabBelumTersedia();

        return view('divisi.keuangan.pendapatan', compact(
            'division', 'pendapatan', 'totalPendapatan', 'awal', 'akhir', 'tab', 'tabList', 'tabBelumTersedia'
        ));
    }

    public function exportPdf(Request $request, Division $division)
    {
        $daftarTab = $this->daftarTab();

        $awal = $request->filled('awal') ? $request->awal : $this->defaultAwal();
        $akhir = $request->filled('akhir') ? $request->akhir : $this->defaultAkhir();
        $tab = $request->query('tab', array_key_first($daftarTab));

        if (! array_key_exists($tab, $daftarTab)) {
            $tab = array_key_first($daftarTab);
        }

        $pendapatan = $this->queryPendapatan($tab, $awal, $akhir)->get();

        $pdf = Pdf::loadView('pdf.keuangan.pendapatan', [
            'pendapatan' => $pendapatan,
            'awal' => $awal,
            'akhir' => $akhir,
            'tab' => $daftarTab[$tab]['label'],
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('data-pendapatan.pdf');
    }
}