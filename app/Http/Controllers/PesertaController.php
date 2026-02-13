<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    public function autocomplete(Request $request)
    {
        $search = $request->nip;

        $data = Peserta::where(function ($query) use ($search) {
            $query->where('nip', 'like', "$search%")
                ->orWhere('nama', 'like', "$search%");
        })
            ->limit(10)
            ->get(['nip', 'nama']);

        return response()->json($data);
    }

    public function fillkabupaten()
    {
        $kabupatenList = [
            'Kubu Raya',
            'Pontianak',
            'Singkawang',
            'Mempawah',
            'Sambas',
            'Bengkayang',
            'Landak',
            'Sintang',
            'Kapuas Hulu',
            'Sekadau',
            'Melawi',
            'Kayong Utara',
            'Ketapang',
            'Sanggau',
            'Kanwil'
        ];

        $pesertas = Peserta::whereNull('kabupaten')->get();

        dd($pesertas);

        foreach ($pesertas as $peserta) {
            foreach ($kabupatenList as $kabupaten) {
                if (stripos($peserta->satker, $kabupaten) !== false) {
                    $peserta->kabupaten = $kabupaten;
                    $peserta->save();
                    break;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Kabupaten berhasil diisi untuk peserta yang belum memiliki kabupaten.',
        ]);
    }
}
