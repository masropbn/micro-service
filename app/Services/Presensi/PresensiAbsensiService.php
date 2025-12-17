<?php

namespace App\Services\Admin;

use App\Models\Presensi\PresensiAbsensi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Presensi\HariLibur;
use Carbon\Carbon;


final readonly class PresensiAbsensiService
{
    public function getListData(): Collection
    {
        return PresensiAbsensi::with(['sdm', 'jadwalKaryawan'])
            ->orderByDesc('tanggal')
            ->get();
    }

    public function findById(string $id): ?PresensiAbsensi
    {
        return PresensiAbsensi::where('id_absensi', $id)->first();
    }

    public function getDetailData(string $id): ?PresensiAbsensi
    {
        return PresensiAbsensi::with(['sdm.person', 'jadwalKaryawan'])
            ->where('id_absensi', $id)
            ->first();
    }

    public function create(array $data): PresensiAbsensi
    {
        $this->validateTanggalAbsensi(
        $data['id_sdm'],
        $data['id_jadwal_karyawan'],
        $data['tanggal']
    );
        return PresensiAbsensi::create($data);
    }

    public function update(PresensiAbsensi $absensi, array $data): PresensiAbsensi
    {
        $absensi->update($data);
        return $absensi->refresh();
    }

    public function delete(string $id): ?PresensiAbsensi
    {
        $absensi = $this->findById($id);

        if (!$absensi) {
            return null;
        }

        $absensi->delete();
        return $absensi;
    }

    private function validateTanggalAbsensi(
        int $idSdm,
        int $idJadwalKaryawan,
        string $tanggal
    ): void {
        $date = Carbon::parse($tanggal);

        // 1. Jumat = libur perusahaan
        if ($date->isFriday()) {
            throw new \Exception('Hari Jumat adalah libur perusahaan');
        }

        // 2. Cek libur nasional / perusahaan
        $isLibur = HariLibur::where('tanggal', $tanggal)
            ->where('is_aktif', 1)
            ->exists();

        if ($isLibur) {
            throw new \Exception('Tanggal tersebut adalah hari libur');
        }

        // 3. Cek jadwal karyawan
        $jadwal = \App\Models\Jadwal\JadwalKaryawan::where('id_jadwal_karyawan', $idJadwalKaryawan)
            ->where('id_sdm', $idSdm)
            ->where('is_aktif', 1)
            ->first();

        if (!$jadwal) {
            throw new \Exception('Jadwal kerja tidak valid');
        }

        // 4. Cek periode jadwal
        if (
            $date->lt($jadwal->tanggal_mulai) ||
            ($jadwal->tanggal_selesai && $date->gt($jadwal->tanggal_selesai))
        ) {
            throw new \Exception('Tanggal di luar periode jadwal kerja');
        }

        // 5. Cek hari kerja
        $hari = $date->format('l'); // Monday, Tuesday, ...
        if (!in_array($hari, $jadwal->hari_kerja)) {
            throw new \Exception('Hari tersebut bukan hari kerja');
        }
    }
}
