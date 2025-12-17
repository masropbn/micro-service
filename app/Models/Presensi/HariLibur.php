<?php

namespace App\Models\Presensi;

use Illuminate\Database\Eloquent\Model;

final class HariLibur extends Model
{
    protected $connection = 'pesonine';
    protected $table = 'hari_libur';
    protected $primaryKey = 'id_hari_libur';

    protected $fillable = [
        'tanggal',
        'jenis_libur',
        'keterangan',
        'is_aktif',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'is_aktif' => 'boolean',
    ];
}
