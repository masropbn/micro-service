<?php

namespace App\Models\Jadwal;

use Illuminate\Database\Eloquent\Model;

final class JadwalKaryawan extends Model
{
    protected $connection = 'simpeg';
    protected $table = 'jadwal_karyawan';
    protected $primaryKey = 'id_jadwal_karyawan';

    protected $casts = [
        'hari_kerja' => 'array',
        'tanggal_mulai' => 'date:Y-m-d',
        'tanggal_selesai' => 'date:Y-m-d',
        'is_aktif' => 'boolean',
    ];
}
