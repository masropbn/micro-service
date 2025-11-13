<?php

namespace App\Models\Person;

use App\Traits\SkipsEmptyAudit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

final class Person extends Model implements Auditable
{
    use AuditableTrait;
    use HasFactory;
    use SkipsEmptyAudit {
        SkipsEmptyAudit::transformAudit insteadof AuditableTrait;
    }

    public $incrementing = true;
    public $timestamps = false;

    protected $table = 'person';
    protected $primaryKey = 'id_person';
    protected $keyType = 'int'; // Tetap int untuk Eloquent
    protected $dateFormat = 'Y-m-d';

    protected $fillable = [
        'id_person',
        'uuid_person', // TAMBAHKAN INI
        'nama',
        'jk',
        'tempat_lahir',
        'tanggal_lahir',
        'kewarganegaraan',
        'golongan_darah',
        'nik',
        'nomor_kk',
        'alamat',
        'rt',
        'rw',
        'id_desa',
        'npwp',
        'nomor_hp',
        'email',
        'foto',
    ];

    protected $guarded = [
        'id_person',
    ];

    protected $casts = [
        'id_person' => 'integer',
        'id_desa' => 'string', // UBAH: karena di table char(10)
        'tanggal_lahir' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        Person::creating(function ($model) {
            if (empty($model->uuid_person)) {
                $model->uuid_person = (string)Str::uuid();
            }
        });
    }

    public function setNamaAttribute($value): void
    {
        $this->attributes['nama'] = strtoupper(trim(strip_tags($value)));
        
        // Truncate jika lebih dari 50 karakter
        if (strlen($this->attributes['nama']) > 50) {
            $this->attributes['nama'] = substr($this->attributes['nama'], 0, 47) . '...';
        }
    }

    public function setTempatLahirAttribute($value): void
    {
        $this->attributes['tempat_lahir'] = trim(strip_tags($value));
        
        // Truncate jika lebih dari 30 karakter
        if (strlen($this->attributes['tempat_lahir']) > 30) {
            $this->attributes['tempat_lahir'] = substr($this->attributes['tempat_lahir'], 0, 27) . '...';
        }
    }

    public function setAlamatAttribute($value): void
    {
        $this->attributes['alamat'] = trim(strip_tags($value));
        
        // Truncate jika lebih dari 100 karakter
        if (strlen($this->attributes['alamat']) > 100) {
            $this->attributes['alamat'] = substr($this->attributes['alamat'], 0, 97) . '...';
        }
    }

    public function setRtAttribute($value): void
    {
        $this->attributes['rt'] = trim(strip_tags($value));
    }

    public function setRwAttribute($value): void
    {
        $this->attributes['rw'] = trim(strip_tags($value));
    }

    public function setNikAttribute($value): void
    {
        $this->attributes['nik'] = trim(strip_tags($value));
    }

    public function setNomorKkAttribute($value): void
    {
        $this->attributes['nomor_kk'] = trim(strip_tags($value));
    }

    public function setIdDesaAttribute($value): void
    {
        $this->attributes['id_desa'] = trim(strip_tags($value));
    }

    public function setNpwpAttribute($value): void
    {
        $this->attributes['npwp'] = $value ? trim(strip_tags($value)) : null;
    }

    public function setNomorHpAttribute($value): void
    {
        $this->attributes['nomor_hp'] = $value ? trim(strip_tags($value)) : null;
    }

    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = $value ? trim(strip_tags($value)) : null;
    }

    public function setKewarganegaraanAttribute($value): void
    {
        $this->attributes['kewarganegaraan'] = $value ? trim($value) : 'Indonesia';
        
        // Truncate jika lebih dari 30 karakter
        if (strlen($this->attributes['kewarganegaraan']) > 30) {
            $this->attributes['kewarganegaraan'] = substr($this->attributes['kewarganegaraan'], 0, 27) . '...';
        }
    }

    public function setGolonganDarahAttribute($value): void
    {
        $this->attributes['golongan_darah'] = $value ? trim(strip_tags($value)) : null;
    }

    public function setFotoAttribute($value): void
    {
        $this->attributes['foto'] = $value ? trim(strip_tags($value)) : null;
    }

    public function getTanggalLahirAttribute($value): ?string
    {
        return $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }
}