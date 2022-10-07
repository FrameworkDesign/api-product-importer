<?php

use Weareframework\ApiProductImporter\Http\Controllers\Web\DashboardController;
use Weareframework\ApiProductImporter\Http\Controllers\Web\SettingsController;
use Weareframework\ApiProductImporter\Http\Controllers\Web\StatamicImportController;
use Weareframework\ApiProductImporter\Http\Controllers\Web\PullApiController;
use Weareframework\ApiProductImporter\Http\Controllers\Web\ApiPulledProductsController;
use Weareframework\ApiProductImporter\Http\Controllers\Api\ApiImportController;

Route::prefix('weareframework/api-product-importer')->group(function () {

    Route::get('/', ['\\'. DashboardController::class, 'index'])->name('weareframework.api-product-importer.dashboard.index');
    Route::get('/settings', ['\\'. SettingsController::class, 'index'])->name('weareframework.api-product-importer.settings.index');
    Route::post('/settings', ['\\'. SettingsController::class, 'update'])->name('weareframework.api-product-importer.settings.update');

    Route::get('/pull', ['\\'. PullApiController::class, 'index'])->name('weareframework.api-product-importer.pull-data.index');
    Route::post('/pull/sku', ['\\'. PullApiController::class, 'single'])->name('weareframework.api-product-importer.pull-data.single.sku');
    Route::get('/delete', ['\\'. PullApiController::class, 'delete'])->name('weareframework.api-product-importer.pull-data.delete');

    Route::match(['GET', 'POST'], '/imported', ['\\'. ApiPulledProductsController::class, 'index'])->name('weareframework.api-product-importer.api-pulled-products.index');

    Route::get('/statamic', ['\\'. StatamicImportController::class, 'index'])->name('weareframework.api-product-importer.import.index');
    Route::get('/statamic/site-target', ['\\'. StatamicImportController::class, 'siteTarget'])->name('weareframework.api-product-importer.statamic.site-target');
    Route::post('/statamic/field-mapping', ['\\'. StatamicImportController::class, 'fieldMapping'])->name('weareframework.api-product-importer.statamic.field-mapping');
    Route::post('/statamic/process-import', ['\\'. StatamicImportController::class, 'processImport'])->name('weareframework.api-product-importer.statamic.process-import');
    Route::get('/statamic/finished', ['\\'. StatamicImportController::class, 'finished'])->name('weareframework.api-product-importer.statamic.finished');
    Route::get('/statamic/clear', ['\\'. StatamicImportController::class, 'clear'])->name('weareframework.api-product-importer.statamic.clear');

    Route::prefix('api')->group(function () {
        Route::get('/', ['\\' . ApiImportController::class, 'index'])->name('weareframework.api-product-importer.api.index');

        Route::get('/pull/{sku}', ['\\' . ApiImportController::class, 'pull'])->name('weareframework.api-product-importer.api.pull');
        Route::post('/store/{sku}', ['\\' . ApiImportController::class, 'store'])->name('weareframework.api-product-importer.api.store');
        Route::get('/poll/{uuid}', ['\\' . ApiImportController::class, 'poll'])->name('weareframework.api-product-importer.api.poll');
    });
});

