<?php

namespace Weareframework\ApiProductImporter\Http\Controllers\Web;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Statamic\Http\Controllers\CP\CpController;

class ImportController extends CpController
{

    public function index(Request $request)
    {
        return view('api-product-importer::import.index');
    }

    public function upload(Request $request)
    {

    }

}
