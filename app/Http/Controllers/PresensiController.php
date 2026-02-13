<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Presensi;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function store(Request $request)
    {
        $sesi = $request->input('sesi');
        $nip = $request->input('nip');

        try {
            $exist = Presensi::where('nip', $nip)->where('sesi', $sesi)->first();

            $peserta = Peserta::where('nip', $nip)->first();

            $nama = "";

            if (!$peserta) {
                $nama = "";
            } else {
                $nama = "<b>" . $peserta->nama . "</b>";
            }

            if ($exist) {
                throw new \Exception('Anda sudah melakukan presensi<br>Selamat datang ' . $nama);
            }

            Presensi::create([
                'sesi' => $sesi,
                'nip' => $nip,
            ]);

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Presensi berhasil dilakukan<br>Selamat datang ' . $nama,
                'data' => $peserta,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
