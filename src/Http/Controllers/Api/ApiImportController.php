<?php

namespace Weareframework\ApiProductImporter\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\SimpleExcel\SimpleExcelReader;
use Statamic\Facades\Collection;
use Statamic\Facades\Config;
use Statamic\Support\Arr;
use Statamic\Facades\Site;
use Illuminate\Http\Request;
use Statamic\Facades\Blueprint;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\UploadedFile;
use Statamic\Http\Controllers\CP\CpController;
use Weareframework\ApiProductImporter\Library\Errors\GeneralError;
use Weareframework\ApiProductImporter\Models\ApiProduct;

class ApiImportController extends CpController
{

    public function index(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => null
            ]);
        } catch (\Exception $exception) {
            return GeneralError::api($exception);
        }
    }

}
