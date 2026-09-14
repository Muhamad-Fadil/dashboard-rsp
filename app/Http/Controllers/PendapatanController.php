<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Pendapatan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PendapatanController extends Controller
{
    /**
     * Daftar tab yang tersedia di halaman Pendapatan.
     */
    private function daftarTab(): array
    {
        return [
            'ringkasan'     => [
                'label' => 'Ringkasan (Semua Kategori)',
                'kode'  => null,
            ],
            'rawat-jalan'   => [
                'label' => 'Rawat Jalan',
                'kode'  => 'PDT-01',
            ],
            'rawat-inap'    => [
                'label' => 'Rawat Inap',
                'kode'  => 'PDT-02',
            ],
            'laboratorium'  => [
                'label' => 'Laboratorium',
                'kode'  => 'PDT-03',
            ],
            'radiologi'     => [
                'label' => 'Radiologi',
                'kode'  => 'PDT-04',
            ],
            'farmasi'       => [
                'label' => 'Farmasi',
                'kode'  => 'PDT-05',
            ],
            'operasi'       => [
                'label' => 'Operasi',
                'kode'  => 'PDT-06',
            ],
            'klaim-bpjs'    => [
                'label' => 'Klaim BPJS',
                'kode'  => 'PDT-07',
            ],
            'gawat-darurat' => [
                'label' => 'Gawat Darurat',
                'kode'  => 'PDT-08',
            ],
        ];
    }

    /**
     * Mengecek kategori yang belum mempunyai data pendapatan.
     */
    private function tabBelumTersedia(): array
    {
        $kodeYangAdaData = Pendapatan::join(
            'kategori_pendapatan',
            'pendapatan.kategori_pendapatan_id',
            '=',
            'kategori_pendapatan.id'
        )
            ->distinct()
            ->pluck('kategori_pendapatan.kode')
            ->all();

        $belum = [];

        foreach ($this->daftarTab() as $kunci => $tab) {
            if (
                $tab['kode'] !== null &&
                !in_array($tab['kode'], $kodeYangAdaData, true)
            ) {
                $belum[] = $kunci;
            }
        }

        return $belum;
    }

    /**
     * Tanggal awal default berdasarkan data pendapatan.
     */
    private function defaultAwal(): string
    {
        return Pendapatan::min('tanggal')
            ?? now()->startOfMonth()->toDateString();
    }

    /**
     * Tanggal akhir default berdasarkan data pendapatan.
     */
    private function defaultAkhir(): string
    {
        return Pendapatan::max('tanggal')
            ?? now()->toDateString();
    }

    /**
     * Query pendapatan berdasarkan tanggal dan tab kategori.
     */
    private function queryPendapatan(
        string $tab,
        string $awal,
        string $akhir
    ) {
        $daftarTab = $this->daftarTab();

        $kodeKategori = $daftarTab[$tab]['kode'] ?? null;

        return Pendapatan::with([
            'kategori',
            'unitKerja',
        ])
            ->whereBetween('tanggal', [$awal, $akhir])
            ->when(
                $kodeKategori,
                function ($query, $kodeKategori) {
                    $query->whereHas(
                        'kategori',
                        function ($q) use ($kodeKategori) {
                            $q->where('kode', $kodeKategori);
                        }
                    );
                }
            )
            ->latest('tanggal');
    }

    /**
     * Halaman utama Pendapatan.
     */
    public function index(
        Request $request,
        Division $division
    ) {
        $daftarTab = $this->daftarTab();

        $awal = $request->filled('awal')
            ? $request->awal
            : $this->defaultAwal();

        $akhir = $request->filled('akhir')
            ? $request->akhir
            : $this->defaultAkhir();

        $tab = $request->query('tab', 'ringkasan');

        if (!array_key_exists($tab, $daftarTab)) {
            $tab = 'ringkasan';
        }

        $pendapatan = $this
            ->queryPendapatan($tab, $awal, $akhir)
            ->get();

        $totalPendapatan = $pendapatan->sum('jumlah');

        $tabList = collect($daftarTab)
            ->map(fn ($t) => $t['label'])
            ->all();

        $tabBelumTersedia = $this->tabBelumTersedia();

        return view(
            'divisi.keuangan.pendapatan',
            compact(
                'division',
                'pendapatan',
                'totalPendapatan',
                'awal',
                'akhir',
                'tab',
                'tabList',
                'tabBelumTersedia'
            )
        );
    }

    /**
     * Export data Pendapatan ke PDF.
     */
    public function exportPdf(
        Request $request,
        Division $division
    ) {
        $daftarTab = $this->daftarTab();

        $awal = $request->filled('awal')
            ? $request->awal
            : $this->defaultAwal();

        $akhir = $request->filled('akhir')
            ? $request->akhir
            : $this->defaultAkhir();

        $tab = $request->query('tab', 'ringkasan');

        if (!array_key_exists($tab, $daftarTab)) {
            $tab = 'ringkasan';
        }

        $pendapatan = $this
            ->queryPendapatan($tab, $awal, $akhir)
            ->get();

        $pdf = Pdf::loadView(
            'pdf.keuangan.pendapatan',
            [
                'pendapatan' => $pendapatan,
                'awal'       => $awal,
                'akhir'      => $akhir,
                'tab'        => $daftarTab[$tab]['label'],
            ]
        )->setPaper('a4', 'landscape');

        return $pdf->stream('data-pendapatan.pdf');
    }
}