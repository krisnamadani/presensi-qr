<?php

namespace App\Models;

use App\Models\Peserta;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $fillable = [
        'sesi',
        'nip',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'nip', 'nip');
    }

    public function getNamaAttribute()
    {
        return $this->peserta ? $this->peserta->nama : '-';
    }

    public function getKabupatenAttribute()
    {
        return $this->peserta ? $this->peserta->kabupaten : '-';
    }

    public function getJamAttribute()
    {
        return $this->created_at ? $this->created_at->format('H:i') : '-';
    }
}
