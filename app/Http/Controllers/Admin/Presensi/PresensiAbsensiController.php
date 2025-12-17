<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PresensiAbsensi\PresensiAbsensiStoreRequest;
use App\Http\Requests\Admin\PresensiAbsensi\PresensiAbsensiUpdateRequest;
use App\Services\Admin\PresensiAbsensiService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

final class PresensiAbsensiController extends Controller
{
    public function __construct(
        private readonly PresensiAbsensiService $service,
        private readonly TransactionService $transaction,
        private readonly ResponseService $response,
    ) {}

    public function index(): View
    {
        return view('admin.presensi-absensi.index');
    }

    public function list(): JsonResponse
    {
        return $this->transaction->handleWithDataTable(
            fn () => $this->service->getListData(),
            [
                'action' => fn ($row) => implode(' ', [
                    $this->transaction->actionButton($row->id_absensi, 'detail'),
                    $this->transaction->actionButton($row->id_absensi, 'edit'),
                    $this->transaction->actionButton($row->id_absensi, 'delete'),
                ]),
            ]
        );
    }

    public function store(PresensiAbsensiStoreRequest $request): JsonResponse
    {
        return $this->transaction->handleWithTransaction(function () use ($request) {
            $data = $this->service->create($request->validated());

            return $this->response->successResponse(
                'Data presensi berhasil dibuat',
                $data,
                201
            );
        });
    }

    public function update(PresensiAbsensiUpdateRequest $request, string $id): JsonResponse
    {
        $absensi = $this->service->findById($id);

        if (!$absensi) {
            return $this->response->errorResponse('Data presensi tidak ditemukan');
        }

        return $this->transaction->handleWithTransaction(function () use ($request, $absensi) {
            $updated = $this->service->update($absensi, $request->validated());

            return $this->response->successResponse(
                'Data presensi berhasil diperbarui',
                $updated
            );
        });
    }

    public function show(string $id): JsonResponse
    {
        return $this->transaction->handleWithShow(function () use ($id) {
            $data = $this->service->getDetailData($id);

            if (!$data) {
                return $this->response->errorResponse('Data presensi tidak ditemukan');
            }

            return $this->response->successResponse(
                'Detail presensi berhasil diambil',
                $data
            );
        });
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->transaction->handleWithTransaction(function () use ($id) {
            $deleted = $this->service->delete($id);

            if (!$deleted) {
                return $this->response->errorResponse('Data presensi tidak ditemukan');
            }

            return $this->response->successResponse(
                'Data presensi berhasil dihapus',
                $deleted
            );
        });
    }
}
