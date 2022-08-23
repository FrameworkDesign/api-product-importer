<?php

namespace Weareframework\ApiProductImporter\Http\Controllers\Web;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Statamic\Http\Controllers\CP\CpController;
use Weareframework\ApiProductImporter\Library\Files\File;
use Weareframework\ApiProductImporter\Library\Settings\CollectSettings;

class ImportController extends CpController
{

    public function index(Request $request, File $file)
    {
        $settings = (new CollectSettings($file))->handle();
        $response = Http::get($settings->values['api_product_importer_route']);
        dd($response->body());
    }


}
