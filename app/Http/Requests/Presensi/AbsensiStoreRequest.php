<?php

namespace App\Http\Requests\Admin\PresensiAbsensi;

use Illuminate\Foundation\Http\FormRequest;

final class PresensiAbsensiStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'id_jadwal_karyawan' => ['required', 'integer'],
            'id_sdm' => ['required', 'integer'],
            'total_jam_kerja' => ['nullable', 'numeric', 'min:0'],
            'total_terlambat' => ['nullable', 'numeric', 'min:0'],
            'total_pulang_awal' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi',
            'id_sdm.required' => 'Karyawan wajib dipilih',
        ];
    }
}
