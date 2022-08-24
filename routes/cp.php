<?php

use Weareframework\ApiProductImporter\Http\Controllers\Web\DashboardController;
use Weareframework\ApiProductImporter\Http\Controllers\Web\SettingsController;
use Weareframework\ApiProductImporter\Http\Controllers\Web\ImportController;
use Weareframework\ApiProductImporter\Http\Controllers\Web\PullApiController;
use Weareframework\ApiProductImporter\Http\Controllers\Api\ApiImportController;

Route::prefix('weareframework/api-product-importer')->group(function () {

    Route::get('/', ['\\'. DashboardController::class, 'index'])->name('weareframework.api-product-importer.dashboard.index');
    Route::get('/settings', ['\\'. SettingsController::class, 'index'])->name('weareframework.api-product-importer.settings.index');
    Route::post('/settings', ['\\'. SettingsController::class, 'update'])->name('weareframework.api-product-importer.settings.update');

    Route::get('/pull', ['\\'. PullApiController::class, 'index'])->name('weareframework.api-product-importer.pull-data.index');
    Route::get('/delete', ['\\'. PullApiController::class, 'delete'])->name('weareframework.api-product-importer.pull-data.delete');

    Route::get('/import', ['\\'. ImportController::class, 'index'])->name('weareframework.api-product-importer.import.index');

    Route::prefix('api')->group(function () {
        Route::get('/', ['\\' . ApiImportController::class, 'index'])->name('weareframework.api-product-importer.api.imported.index');
    });
});

