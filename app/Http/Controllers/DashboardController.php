<?php

namespace App\Http\Controllers;

use App\Models\Pemeriksaan;
use App\Models\Anak;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAnak = Anak::count();
        $totalPemeriksaan = Pemeriksaan::count();
        $totalPemeriksaanHariIni = Pemeriksaan::whereDate('tanggal_periksa', Carbon::today())->count();

        // Pemeriksaan per bulan
        $pemeriksaanPerBulan = Pemeriksaan::selectRaw('MONTH(tanggal_periksa) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_periksa', 2025)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        // Siapkan array dengan 12 bulan
        $dataChart = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataChart[] = $pemeriksaanPerBulan[$i] ?? 0;
        }

        // Ambil 5 pemeriksaan terakhir dengan relasi anak
        $riwayatPemeriksaan = Pemeriksaan::with(['anak', 'petugas'])
            ->orderBy('tanggal_periksa', 'desc')
            ->take(5)
            ->get();

        return view('backend.pages.dashboard', compact(
            'totalAnak',
            'totalPemeriksaan',
            'totalPemeriksaanHariIni',
            'dataChart',
            'riwayatPemeriksaan'
        ));
    }
}
