<?php

namespace App\Http\Controllers\Admin\Person;

use App\Http\Controllers\Controller;
use App\Http\Requests\Person\PersonStoreRequest;
use App\Http\Requests\Person\PersonUpdateRequest;
use App\Services\Person\PersonService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PersonController extends Controller
{
    public function __construct(
        private readonly PersonService      $personService,
        private readonly TransactionService $transactionService,
        private readonly ResponseService    $responseService,
    )
    {
    }

    public function index(): View
    {
        return view('admin.person.index');
    }

    public function list(): JsonResponse
    {
        return $this->transactionService->handleWithDataTable(
            fn() => $this->personService->getListData(),
            [
                'action' => fn($row) => implode(' ', [
                    $this->transactionService->actionButton($row->id_person, 'detail'),
                    $this->transactionService->actionButton($row->id_person, 'edit'),
                ]),
            ]
        );
    }

    public function listApi(): JsonResponse
    {
        return $this->transactionService->handleWithDataTable(
            fn() => $this->personService->getListData()
        );
    }

    public function store(PersonStoreRequest $request): JsonResponse
    {
        $foto = $request->file('foto');

        return $this->transactionService->handleWithTransaction(function () use ($request, $foto) {
            $payload = $request->only([
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
            ]);

            $created = $this->personService->create($payload);

            if ($foto) {
                $uploadResult = $this->personService->handleFileUpload($foto);
                $created->update(['foto' => $uploadResult['file_name']]);
            }

            return $this->responseService->successResponse('Data berhasil dibuat', $created, 201);
        });
    }

    public function update(PersonUpdateRequest $request, string $id): JsonResponse
    {
        $data = $this->personService->findById($id);
        if (!$data) {
            return $this->responseService->errorResponse('Data tidak ditemukan');
        }

        $foto = $request->file('foto');

        return $this->transactionService->handleWithTransaction(function () use ($request, $data, $foto) {
            $payload = $request->only([
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
            ]);

            $updatedData = $this->personService->update($data, $payload);

            if ($foto) {
                $uploadResult = $this->personService->handleFileUpload($foto, $updatedData);
                $updatedData->update(['foto' => $uploadResult['file_name']]);
            }

            return $this->responseService->successResponse('Data berhasil diperbarui', $updatedData);
        });
    }

    public function show(string $id): JsonResponse
    {
        return $this->transactionService->handleWithShow(function () use ($id) {
            $data = $this->personService->getDetailData($id);

            return $this->responseService->successResponse('Data berhasil diambil', $data);
        });
    }

    public function destroy(string $id){
        return $this->transactionService->handleWithTransaction(function () use($id){
            $deleteData = $this->personService->deleteData($id);

            return $this->responseService->successResponse('Data berhasil dihapus', $deleteData);
        });
    }
}
