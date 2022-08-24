<?php

namespace Weareframework\ApiProductImporter\Http\Controllers\Web;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Statamic\Http\Controllers\CP\CpController;
use Weareframework\ApiProductImporter\Models\ApiProduct;

class PullApiController extends CpController
{

    public function index(Request $request)
    {
        Artisan::call('api-product-importer:products:import');
        session()->now('success', __('Started, check back soon'));
        session()->flash('Started, check back soon', __('api-product-importer-import'));
        return redirect()->route('statamic.cp.weareframework.api-product-importer.dashboard.index');
    }

    public function delete(Request $request)
    {
        session()->now('success', __('All Deleted'));
        ApiProduct::whereNotNull('sku')->delete();
        return redirect()->route('statamic.cp.weareframework.api-product-importer.dashboard.index');
    }

}
