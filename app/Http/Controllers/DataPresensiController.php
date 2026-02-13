<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataPresensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Presensi::with('peserta');

        if ($request->filled('sesi')) {
            $query->where('sesi', $request->sesi);
        }

        if ($request->filled('kabupaten')) {
            $query->whereHas('peserta', function ($q) use ($request) {
                $q->where('kabupaten', $request->kabupaten);
            });
        }

        $presensis = $query->orderByDesc('id')->get();

        $sesis = Presensi::orderBy('sesi')
            ->distinct()
            ->pluck('sesi');
        $kabupatens = Peserta::select('kabupaten')
            ->distinct()
            ->orderBy('kabupaten')
            ->pluck('kabupaten');

        $data = DB::table('pesertas')
            ->leftJoin('presensis', 'pesertas.nip', '=', 'presensis.nip')
            ->select(
                'pesertas.kabupaten',
                DB::raw('COUNT(DISTINCT pesertas.nip) as total_peserta'),
                DB::raw('COUNT(DISTINCT presensis.nip) as jumlah_hadir')
            )
            ->groupBy('pesertas.kabupaten')
            ->orderBy('pesertas.kabupaten')
            ->get();


        return view('presensi.index', compact('presensis', 'sesis', 'kabupatens', 'data'));
    }
}
