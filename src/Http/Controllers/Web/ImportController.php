<?php

namespace Weareframework\ApiProductImporter\Http\Controllers\Web;

use Illuminate\Http\Request;
use Statamic\Http\Controllers\CP\CpController;

class ImportController extends CpController
{

    public function index(Request $request)
    {
        return 'import controller';
    }



}
