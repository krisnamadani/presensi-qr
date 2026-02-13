<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataPesertaController extends Controller
{
    public function index(Request $request)
    {
        $kabupatens = DB::table('pesertas')
            ->select('kabupaten')
            ->distinct()
            ->orderBy('kabupaten')
            ->pluck('kabupaten');

        $sesis = DB::table('presensis')
            ->select('sesi')
            ->distinct()
            ->orderBy('sesi')
            ->pluck('sesi');

        $query = DB::table('pesertas')
            ->leftJoin('presensis', 'pesertas.nip', '=', 'presensis.nip')
            ->select(
                'pesertas.nip',
                'pesertas.nama',
                'pesertas.kabupaten',
                'presensis.sesi'
            );

        if ($request->kabupaten) {
            $query->where('pesertas.kabupaten', $request->kabupaten);
        }

        $pesertas = $query->get()->groupBy('nip');

        $jumlahPeserta = $pesertas->count();

        $jumlahHadir = $pesertas->filter(function ($rows) {
            return $rows->pluck('sesi')->filter()->count() > 0;
        })->count();

        $jumlahTidakHadir = $jumlahPeserta - $jumlahHadir;

        return view('peserta.index', compact('pesertas', 'sesis', 'kabupatens', 'jumlahPeserta', 'jumlahHadir', 'jumlahTidakHadir'));
    }
}
