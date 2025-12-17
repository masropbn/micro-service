<?php

namespace App\Http\Requests\Admin\PresensiAbsensi;

use Illuminate\Foundation\Http\FormRequest;

final class PresensiAbsensiUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['sometimes', 'date'],
            'id_jadwal_karyawan' => ['sometimes', 'integer'],
            'id_sdm' => ['sometimes', 'integer'],
            'total_jam_kerja' => ['nullable', 'numeric', 'min:0'],
            'total_terlambat' => ['nullable', 'numeric', 'min:0'],
            'total_pulang_awal' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
