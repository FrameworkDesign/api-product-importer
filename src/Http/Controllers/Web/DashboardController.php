<?php

namespace Weareframework\ApiProductImporter\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Site;
use Statamic\Http\Controllers\CP\CpController;
use Weareframework\ApiProductImporter\Library\Files\File;
use Weareframework\ApiProductImporter\Library\Settings\CollectSettings;
use Weareframework\ApiProductImporter\Models\ApiProduct;

class DashboardController extends CpController
{

    public function index(Request $request, File $file)
    {
        $queueSize = Queue::size();
        $apiParentProducts = ApiProduct::whereNull('parent_sku')->get();
        $allApiProducts = ApiProduct::all('id');
        $settings = (new CollectSettings($file))->handle();
        return view('api-product-importer::dashboard.index', [
            'apiParentProducts' => $apiParentProducts,
            'allApiProducts' => $allApiProducts,
            'settings' => $settings,
            'queueSize' => $queueSize
        ]);
    }

}
