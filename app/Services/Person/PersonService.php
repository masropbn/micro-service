<?php

namespace App\Services\Person;

use App\Models\Person\Person;
use App\Services\Tools\FileUploadService;
use Illuminate\Support\Collection;
use Exception;

final readonly class PersonService
{
    public function __construct(
        private FileUploadService $fileUploadService,
    ) {}

    public function getListData(): Collection
    {
        return Person::select([
            'id_person',
            'uuid_person',
            'nama',
            'jk',
            'tempat_lahir',
            'tanggal_lahir',
            'nik',
            'nomor_kk',
            'npwp',
            'nomor_hp',
            'email',
            'foto',
        ])->orderBy('nama')->get();
    }

    public function create(array $data): Person
    {
        // Pastikan id_desa dalam format string jika table char(10)
        if (isset($data['id_desa']) && is_numeric($data['id_desa'])) {
            $data['id_desa'] = (string) $data['id_desa'];
        }

        // Validasi data sebelum create
        $this->validatePersonData($data);

        return Person::create($data);
    }

    public function getDetailData(string $id): ?Person
    {
        return Person::query()
            ->leftJoin('ref_almt_desa', 'person.id_desa', '=', 'ref_almt_desa.id_desa')
            ->leftJoin('ref_almt_kecamatan', 'ref_almt_desa.id_kecamatan', '=', 'ref_almt_kecamatan.id_kecamatan')
            ->leftJoin('ref_almt_kabupaten', 'ref_almt_kecamatan.id_kabupaten', '=', 'ref_almt_kabupaten.id_kabupaten')
            ->leftJoin('ref_almt_provinsi', 'ref_almt_kabupaten.id_provinsi', '=', 'ref_almt_provinsi.id_provinsi')
            ->select([
                'person.*',
                'ref_almt_desa.desa',
                'ref_almt_kecamatan.id_kecamatan',
                'ref_almt_kecamatan.kecamatan',
                'ref_almt_kabupaten.id_kabupaten',
                'ref_almt_kabupaten.kabupaten',
                'ref_almt_provinsi.id_provinsi',
                'ref_almt_provinsi.provinsi',
            ])
            ->where('person.id_person', $id)
            ->first();
    }

    public function findById(string $id): ?Person
    {
        return Person::find($id);
    }

    public function update(Person $person, array $data): Person
    {
        // Pastikan id_desa dalam format string jika table char(10)
        if (isset($data['id_desa']) && is_numeric($data['id_desa'])) {
            $data['id_desa'] = (string) $data['id_desa'];
        }

        // Validasi data sebelum update
        $this->validatePersonData($data);

        $person->update($data);
        return $person;
    }

    public function delete(Person $person): bool
    {
        // Delete file foto jika ada
        if ($person->foto) {
            $this->deleteFoto($person->foto);
        }

        return $person->delete();
    }

    public function handleFileUpload($foto, ?Person $person = null): ?array
    {
        if (!$foto) {
            return null;
        }

        if ($person && $person->foto) {
            return $this->fileUploadService->updateFileByType($foto, $person->foto, 'person_foto');
        }

        return $this->fileUploadService->uploadByType($foto, 'person_foto');
    }

    public function deleteFoto(string $filename): bool
    {
        return $this->fileUploadService->deleteFileByType($filename, 'person_foto');
    }

    public function findByNik(string $nik): ?Person
    {
        return Person::where('nik', $nik)->first();
    }

    public function getPersonDetailByUuid(string $uuid): ?Person
    {
        return Person::where('uuid_person', $uuid)->first();
    }

    /**
     * Validasi data sesuai constraints table
     */
    private function validatePersonData(array $data): void
    {
        $constraints = [
            'nama' => 50,
            'tempat_lahir' => 30,
            'kewarganegaraan' => 30,
            'alamat' => 100,
            'email' => 100,
        ];

        foreach ($constraints as $field => $maxLength) {
            if (isset($data[$field]) && strlen($data[$field]) > $maxLength) {
                throw new Exception("Field {$field} melebihi batas maksimal {$maxLength} karakter");
            }
        }

        // Validasi enum values
        if (isset($data['jk']) && !in_array($data['jk'], ['L', 'P'])) {
            throw new Exception("Jenis kelamin harus L atau P");
        }

        if (isset($data['golongan_darah']) && !in_array($data['golongan_darah'], ['A', 'B', 'AB', 'O', null])) {
            throw new Exception("Golongan darah harus A, B, AB, atau O");
        }
    }
}
