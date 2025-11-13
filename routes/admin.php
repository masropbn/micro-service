<?php

use App\Http\Controllers\Admin\Master\MasterJabatanController;
use App\Http\Controllers\Admin\Master\MasterPeriodeController;
use App\Http\Controllers\Admin\Master\MasterUnitController;
use App\Http\Controllers\Admin\Person\PersonAsuransiController;
use App\Http\Controllers\Admin\Person\PersonController;
use App\Http\Controllers\Admin\Ref\RefEselonController;
use App\Http\Controllers\Admin\Ref\RefHubunganKeluargaController;
use App\Http\Controllers\Admin\Ref\RefJenisAsuransiController;
use App\Http\Controllers\Admin\Ref\RefJenjangPendidikanController;
use App\Http\Controllers\Admin\Sdm\PersonSdmController;
use App\Http\Controllers\Admin\Sdm\SdmKeluargaController;
use App\Http\Controllers\Admin\Sdm\SdmRekeningController;
use App\Http\Controllers\Admin\Sdm\SdmRiwayatPendidikanController;
use App\Http\Controllers\Admin\Sdm\SdmStrukturalController;
use App\Http\Controllers\Content\PortalController;
use Illuminate\Support\Facades\Route;

Route::get('view-file/{folder}/{filename}', [PortalController::class, 'viewFile'])
    ->where(['folder' => '[A-Za-z0-9_\-]+', 'filename' => '[A-Za-z0-9_\-\.]+'])
    ->name('view-file');

Route::prefix('person')->group(function () {
    Route::get('/', [PersonController::class, 'index'])
        ->name('person.index');
    Route::get('data', [PersonController::class, 'list'])
        ->name('person.list');
    Route::get('show/{id}', [PersonController::class, 'show'])
        ->name('person.show');
    Route::post('/store', [PersonController::class, 'store'])
        ->name('person.store');
    Route::post('update/{id}', [PersonController::class, 'update'])
        ->name('person.update');

    Route::get('test-store', [PersonController::class, 'testStore'])
        ->name('person.test-store');
    Route::get('test-list', [PersonController::class, 'testList'])
        ->name('person.test-list');
    Route::get('test-detail/{id}', [PersonController::class, 'testDetail'])
        ->name('person.test-detail');
});



Route::prefix('sdm')->group(function () {
    Route::get('/', [PersonSdmController::class, 'index'])
        ->name('sdm.sdm.index');
    Route::get('data', [PersonSdmController::class, 'list'])
        ->name('sdm.sdm.list');
    Route::get('show/{id}', [PersonSdmController::class, 'show'])
        ->name('sdm.sdm.show');
    Route::post('/store', [PersonSdmController::class, 'store'])
        ->name('sdm.sdm.store');
    Route::post('update/{id}', [PersonSdmController::class, 'update'])
        ->name('sdm.sdm.update');
    Route::get('histori/{id}', [PersonSdmController::class, 'histori'])
        ->name('sdm.sdm.histori');
    Route::get('find/by/nik/{id}', [PersonSdmController::class, 'find_by_nik'])
        ->name('sdm.sdm.find_by_nik');

    Route::prefix('riwayat-pendidikan')->group(function () {
        Route::get('/{id}', [SdmRiwayatPendidikanController::class, 'index'])
            ->name('sdm.riwayat-pendidikan.index');
        Route::get('data/{id}', [SdmRiwayatPendidikanController::class, 'list'])
            ->name('sdm.riwayat-pendidikan.list');
        Route::get('show/{id}', [SdmRiwayatPendidikanController::class, 'show'])
            ->name('sdm.riwayat-pendidikan.show');
        Route::post('/store', [SdmRiwayatPendidikanController::class, 'store'])
            ->name('sdm.riwayat-pendidikan.store');
        Route::post('update/{id}', [SdmRiwayatPendidikanController::class, 'update'])
            ->name('sdm.riwayat-pendidikan.update');
        Route::post('destroy/{id}', [SdmRiwayatPendidikanController::class, 'destroy'])
            ->name('sdm.riwayat-pendidikan.destroy');
    });

    Route::prefix('keluarga')->group(function () {
        Route::get('/{id}', [SdmKeluargaController::class, 'index'])
            ->name('sdm.keluarga.index');
        Route::get('data/{id}', [SdmKeluargaController::class, 'list'])
            ->name('sdm.keluarga.list');
        Route::get('show/{id}', [SdmKeluargaController::class, 'show'])
            ->name('sdm.keluarga.show');
        Route::post('/store', [SdmKeluargaController::class, 'store'])
            ->name('sdm.keluarga.store');
        Route::post('update/{id}', [SdmKeluargaController::class, 'update'])
            ->name('sdm.keluarga.update');
        Route::post('destroy/{id}', [SdmKeluargaController::class, 'destroy'])
            ->name('sdm.keluarga.destroy');
        Route::get('find/by/nik/{id}', [SdmKeluargaController::class, 'find_by_nik'])
            ->name('sdm.keluarga.find_by_nik');
    });

    Route::prefix('asuransi')->group(function () {
        Route::get('/{id}', [PersonAsuransiController::class, 'index'])
            ->name('sdm.asuransi.index');
        Route::get('data/{id}', [PersonAsuransiController::class, 'list'])
            ->name('sdm.asuransi.list');
        Route::get('show/{id}', [PersonAsuransiController::class, 'show'])
            ->name('sdm.asuransi.show');
        Route::post('/store', [PersonAsuransiController::class, 'store'])
            ->name('sdm.asuransi.store');
        Route::post('update/{id}', [PersonAsuransiController::class, 'update'])
            ->name('sdm.asuransi.update');
        Route::post('destroy/{id}', [PersonAsuransiController::class, 'destroy'])
            ->name('sdm.asuransi.destroy');
        Route::get('find/by/nik/{id}', [PersonAsuransiController::class, 'find_by_nik'])
            ->name('sdm.asuransi.find_by_nik');
    });

    Route::prefix('rekening')->group(function () {
        Route::get('/{id}', [SdmRekeningController::class, 'index'])
            ->name('sdm.rekening.index');
        Route::get('data/{id}', [SdmRekeningController::class, 'list'])
            ->name('sdm.rekening.list');
        Route::get('show/{id}', [SdmRekeningController::class, 'show'])
            ->name('sdm.rekening.show');
        Route::post('/store', [SdmRekeningController::class, 'store'])
            ->name('sdm.rekening.store');
        Route::post('update/{id}', [SdmRekeningController::class, 'update'])
            ->name('sdm.rekening.update');
        Route::post('destroy/{id}', [SdmRekeningController::class, 'destroy'])
            ->name('sdm.rekening.destroy');
    });

    Route::prefix('struktural')->group(function () {
        Route::get('/{id}', [SdmStrukturalController::class, 'index'])
            ->name('sdm.struktural.index');
        Route::get('data/{id}', [SdmStrukturalController::class, 'list'])
            ->name('sdm.struktural.list');
        Route::get('show/{id}', [SdmStrukturalController::class, 'show'])
            ->name('sdm.struktural.show');
        Route::post('/store', [SdmStrukturalController::class, 'store'])
            ->name('sdm.struktural.store');
        Route::post('update/{id}', [SdmStrukturalController::class, 'update'])
            ->name('sdm.struktural.update');
        Route::post('destroy/{id}', [SdmStrukturalController::class, 'destroy'])
            ->name('sdm.struktural.destroy');
    });
});

Route::prefix('master')->group(function () {
    Route::prefix('periode')->group(function () {
        Route::get('/', [MasterPeriodeController::class, 'index'])
            ->name('master.periode.index');
        Route::get('data', [MasterPeriodeController::class, 'list'])
            ->name('master.periode.list');
        Route::get('show/{id}', [MasterPeriodeController::class, 'show'])
            ->name('master.periode.show');
        Route::post('/store', [MasterPeriodeController::class, 'store'])
            ->name('master.periode.store');
        Route::post('update/{id}', [MasterPeriodeController::class, 'update'])
            ->name('master.periode.update');
    });

    Route::prefix('unit')->group(function () {
        Route::get('/', [MasterUnitController::class, 'index'])
            ->name('master.unit.index');
        Route::get('data', [MasterUnitController::class, 'list'])
            ->name('master.unit.list');
        Route::get('show/{id}', [MasterUnitController::class, 'show'])
            ->name('master.unit.show');
        Route::post('/store', [MasterUnitController::class, 'store'])
            ->name('master.unit.store');
        Route::post('update/{id}', [MasterUnitController::class, 'update'])
            ->name('master.unit.update');
    });

    Route::prefix('jabatan')->group(function () {
        Route::get('/', [MasterJabatanController::class, 'index'])
            ->name('master.jabatan.index');
        Route::get('data', [MasterJabatanController::class, 'list'])
            ->name('master.jabatan.list');
        Route::get('show/{id}', [MasterJabatanController::class, 'show'])
            ->name('master.jabatan.show');
        Route::post('/store', [MasterJabatanController::class, 'store'])
            ->name('master.jabatan.store');
        Route::post('update/{id}', [MasterJabatanController::class, 'update'])
            ->name('master.jabatan.update');
    });

});

Route::prefix('ref')->group(function () {
    Route::prefix('jenjang-pendidikan')->group(function () {
        Route::get('/', [RefJenjangPendidikanController::class, 'index'])
            ->name('ref.jenjang-pendidikan.index');
        Route::get('data', [RefJenjangPendidikanController::class, 'list'])
            ->name('ref.jenjang-pendidikan.list');
        Route::get('show/{id}', [RefJenjangPendidikanController::class, 'show'])
            ->name('ref.jenjang-pendidikan.show');
        Route::post('/store', [RefJenjangPendidikanController::class, 'store'])
            ->name('ref.jenjang-pendidikan.store');
        Route::post('update/{id}', [RefJenjangPendidikanController::class, 'update'])
            ->name('ref.jenjang-pendidikan.update');
    });

    Route::prefix('hubungan-keluarga')->group(function () {
        Route::get('/', [RefHubunganKeluargaController::class, 'index'])
            ->name('ref.hubungan-keluarga.index');
        Route::get('data', [RefHubunganKeluargaController::class, 'list'])
            ->name('ref.hubungan-keluarga.list');
        Route::get('show/{id}', [RefHubunganKeluargaController::class, 'show'])
            ->name('ref.hubungan-keluarga.show');
        Route::post('/store', [RefHubunganKeluargaController::class, 'store'])
            ->name('ref.hubungan-keluarga.store');
        Route::post('update/{id}', [RefHubunganKeluargaController::class, 'update'])
            ->name('ref.hubungan-keluarga.update');
    });

    Route::prefix('jenis-asuransi')->group(function () {
        Route::get('/', [RefJenisAsuransiController::class, 'index'])
            ->name('ref.jenis-asuransi.index');
        Route::get('data', [RefJenisAsuransiController::class, 'list'])
            ->name('ref.jenis-asuransi.list');
        Route::get('show/{id}', [RefJenisAsuransiController::class, 'show'])
            ->name('ref.jenis-asuransi.show');
        Route::post('/store', [RefJenisAsuransiController::class, 'store'])
            ->name('ref.jenis-asuransi.store');
        Route::post('update/{id}', [RefJenisAsuransiController::class, 'update'])
            ->name('ref.jenis-asuransi.update');
    });

    Route::prefix('eselon')->group(function () {
        Route::get('/', [RefEselonController::class, 'index'])
            ->name('ref.eselon.index');
        Route::get('data', [RefEselonController::class, 'list'])
            ->name('ref.eselon.list');
        Route::get('show/{id}', [RefEselonController::class, 'show'])
            ->name('ref.eselon.show');
        Route::post('/store', [RefEselonController::class, 'store'])
            ->name('ref.eselon.store');
        Route::post('update/{id}', [RefEselonController::class, 'update'])
            ->name('ref.eselon.update');
    });
});
