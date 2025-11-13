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
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;

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
                    $this->transactionService->actionButton($row->id_person, 'delete'),
                ]),
            ]
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
                if ($uploadResult) {
                    $created->update(['foto' => $uploadResult['file_name']]);
                }
            }

            return $this->responseService->successResponse('Data berhasil dibuat', $created, 201);
        });
    }

    public function update(PersonUpdateRequest $request, string $id): JsonResponse
    {
        $data = $this->personService->findById($id);
        if (!$data) {
            return $this->responseService->errorResponse('Data tidak ditemukan', 404);
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
                if ($uploadResult) {
                    $updatedData->update(['foto' => $uploadResult['file_name']]);
                }
            }

            return $this->responseService->successResponse('Data berhasil diperbarui', $updatedData);
        });
    }

    public function show(string $id): JsonResponse
    {
        return $this->transactionService->handleWithShow(function () use ($id) {
            $data = $this->personService->getDetailData($id);

            if (!$data) {
                return $this->responseService->errorResponse('Data tidak ditemukan', 404);
            }

            return $this->responseService->successResponse('Data berhasil diambil', $data);
        });
    }

    /**
     * METHOD TEST STORE - UNTUK DEBUGGING
     */
    public function testStore(): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Data test yang sesuai dengan struktur table
            $testData = [
                'uuid_person' => \Illuminate\Support\Str::uuid(),
                'nama' => 'JOHN DOE TEST',
                'jk' => 'L',
                'tempat_lahir' => 'JAKARTA',
                'tanggal_lahir' => '1990-01-15',
                'kewarganegaraan' => 'INDONESIA',
                'golongan_darah' => 'A',
                'nik' => '1234567890' . rand(100000, 999999), // NIK unik
                'nomor_kk' => '9876543210' . rand(100000, 999999),
                'alamat' => 'JL. TEST NO. 123',
                'rt' => '001',
                'rw' => '002',
                'id_desa' => '1101012001', // Sesuaikan dengan format char(10)
                'npwp' => '123456789012345',
                'nomor_hp' => '081234567890',
                'email' => 'test' . rand(1000, 9999) . '@test.com'
            ];

            // Gunakan PersonService untuk create
            $created = $this->personService->create($testData);

            DB::commit();

            return $this->responseService->successResponse(
                'Test data berhasil dibuat', 
                [
                    'id_person' => $created->id_person,
                    'uuid_person' => $created->uuid_person,
                    'nama' => $created->nama,
                    'nik' => $created->nik
                ],
                201
            );

        } catch (QueryException $e) {
            DB::rollBack();
            
            return $this->responseService->errorResponse(
                'Database Error: ' . $e->getMessage(),
                500
            );
        } catch (Exception $e) {
            DB::rollBack();
            
            return $this->responseService->errorResponse(
                'Error: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * METHOD TEST LIST - UNTUK DEBUGGING
     */
    public function testList(): JsonResponse
    {
        try {
            $persons = $this->personService->getListData();

            return $this->responseService->successResponse(
                'Data test berhasil diambil',
                [
                    'total' => $persons->count(),
                    'data' => $persons->take(5) // Ambil 5 data pertama
                ]
            );

        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'Error: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * METHOD TEST DETAIL - UNTUK DEBUGGING
     */
    public function testDetail($id): JsonResponse
    {
        try {
            $person = $this->personService->findById($id);

            if (!$person) {
                return $this->responseService->errorResponse('Data tidak ditemukan', 404);
            }

            return $this->responseService->successResponse(
                'Detail data berhasil diambil',
                $person
            );

        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'Error: ' . $e->getMessage(),
                500
            );
        }
    }
}