<?php

namespace App\Services\Tools;

use Closure;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

final class TransactionService
{
    private ResponseService $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    public function handleWithDataTable(Closure $queryCallback, array $extraColumns = []): JsonResponse
    {
        try {
            $query = $queryCallback();

            $dataTable = DataTables::of($query);
            foreach ($extraColumns as $name => $callback) {
                $dataTable->addColumn($name, $callback);
            }
            if (!empty($extraColumns)) {
                $dataTable->rawColumns(array_keys($extraColumns));
            }

            return $dataTable->toJson();
        } catch (Exception $exception) {
            Log::error($exception->getMessage(), ['exception' => $exception]);

            return response()->json(['draw' => 0, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => [], 'error' => 'Terjadi kesalahan yang tidak terduga.'], 500);
        }
    }

    // METHOD INI SEKARANG TIDAK DIPAKAI KARENA MANUAL DI CONTROLLER
    // Tapi kita keep untuk kompatibilitas
    public function handleWithTransaction(callable $callback): JsonResponse
    {
        try {
            return \Illuminate\Support\Facades\DB::transaction(static fn() => $callback());
        } catch (QueryException $exception) {
            $userMessage = $this->getQueryErrorMessage($exception->getCode());
            Log::error($exception->getMessage(), ['exception' => $exception]);

            return $this->responseService->errorResponse($userMessage, 500);
        } catch (Exception $exception) {
            Log::error($exception->getMessage(), ['exception' => $exception]);

            return $this->responseService->errorResponse('Terjadi kesalahan yang tidak terduga.', 500);
        } catch (Throwable $throwable) {
            Log::error($throwable->getMessage(), ['exception' => $throwable]);

            return $this->responseService->errorResponse('Terjadi kesalahan yang tidak terduga.', 500);
        }
    }

    // METHOD BARU: Untuk operasi tanpa transaction
    public function handleWithOperation(callable $callback): JsonResponse
    {
        try {
            $result = $callback();
            return $result;
        } catch (QueryException $exception) {
            $userMessage = $this->getQueryErrorMessage($exception->getCode());
            Log::error($exception->getMessage(), ['exception' => $exception]);

            return $this->responseService->errorResponse($userMessage, 500);
        } catch (Exception $exception) {
            Log::error($exception->getMessage(), ['exception' => $exception]);

            return $this->responseService->errorResponse('Terjadi kesalahan yang tidak terduga.', 500);
        }
    }

    public function handleWithShow(callable $callback): JsonResponse
    {
        try {
            return $callback();
        } catch (Exception $exception) {
            Log::error($exception->getMessage(), ['exception' => $exception]);

            return $this->responseService->errorResponse('Terjadi kesalahan yang tidak terduga.', 500);
        }
    }

    public function actionButton(string $encryptedId, string $type): string
    {
        $icons = ['detail' => 'bi-file-text', 'edit' => 'bi-pencil', 'delete' => 'bi-trash'];

        $targets = ['detail' => '#form_detail', 'edit' => '#form_edit'];

        $icon = $icons[$type] ?? 'bi-question-circle';
        $target = $targets[$type] ?? '#';

        if ($type === 'delete') {
            return " <button type='button'
                    title='" . ucfirst($type) . "' 
                    onclick='deleteConfirmation(\"$encryptedId\")'
                    class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1 '>
                    <span class='bi $icon' aria-hidden='true'></span>
                </button>";
        }

        return "
        <button type='button'
            data-id='$encryptedId' 
            title='" . ucfirst($type) . "' 
            data-bs-toggle='modal' 
            data-bs-target='$target' 
            aria-label='" . ucfirst($type) . "' 
            class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1 '>
            <span class='bi $icon' aria-hidden='true'></span>
        </button>";
    }

    public function actionLink(string $route, string $type, string $title): string
    {
        $icons = ['histori' => 'bi bi-folder-plus'];

        $icon = $icons[$type] ?? 'bi-question-circle';

        return "
            <a href='$route' 
            title='$title' 
            class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm me-1'>
                <span class='bi $icon' aria-hidden='true'></span>
            </a>";
    }

    private function getQueryErrorMessage(string $errorCode): string
    {
        $defaultMessage = 'Terjadi kesalahan pada basis data. Silakan coba lagi nanti.';
        $errorMessages = [
            '23000' => 'Operasi tidak dapat diselesaikan karena pelanggaran integritas data.',
            '23505' => 'Terjadi pelanggaran batasan unik. Pastikan semua data bersifat unik.',
            '23503' => 'Terjadi pelanggaran batasan kunci asing. Periksa dependensi data.',
            '42P01' => 'Tabel yang ditentukan tidak ada. Periksa nama tabel dan coba lagi.',
            '42703' => 'Kolom yang ditentukan tidak ditemukan dalam basis data.',
            '42601' => 'Terdapat kesalahan sintaks pada kueri SQL. Periksa sintaks kueri.',
            '40001' => 'Terjadi kesalahan tingkat isolasi transaksi. Silakan coba ulang transaksi.',
            '40P01' => 'Deadlock terdeteksi. Silakan coba ulang operasi.',
            '22007' => 'Format tanggal/waktu tidak valid. Perbaiki format dan coba lagi.',
            '22008' => 'Overflow pada field tanggal/waktu. Sesuaikan nilai tanggal/waktu.',
            '23502' => 'Pelanggaran batasan not-null. Pastikan semua field yang diperlukan diisi.',
            '23514' => 'Terjadi pelanggaran batasan cek. Pastikan data memenuhi semua batasan.'
        ];

        return $errorMessages[$errorCode] ?? $defaultMessage;
    }
}