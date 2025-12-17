<?php

namespace App\Models\Presensi;

use App\Models\Jadwal\JadwalKaryawan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\Sdm\PersonSdm;

final class PresensiAbsensi extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $connection = 'pesonine';
    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';
    public $timestamps = true;

    protected $fillable = [
        'tanggal',
        'id_jadwal_karyawan',
        'id_sdm',
        'total_jam_kerja',
        'total_terlambat',
        'total_pulang_awal',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'total_jam_kerja' => 'decimal:2',
        'total_terlambat' => 'decimal:2',
        'total_pulang_awal' => 'decimal:2',
    ];

    public function jadwalKaryawan()
    {
        return $this->belongsTo(
            JadwalKaryawan::class,
            'id_jadwal_karyawan'
        );
    }

    public function sdm()
    {
        return $this->belongsTo(
            PersonSdm::class,
            'id_sdm'
        );
    }

    public function getJamKerjaEfektifAttribute(): float
    {
        return max(
            0,
            ($this->total_jam_kerja ?? 0)
            - ($this->total_terlambat ?? 0)
            - ($this->total_pulang_awal ?? 0)
        );
    }
}
