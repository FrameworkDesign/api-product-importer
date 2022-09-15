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
        try {
            Artisan::call('api-product-importer:products:import');
            session()->flash('success', 'Started, check back soon');
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
        }
        return redirect()->route('statamic.cp.weareframework.api-product-importer.dashboard.index');
    }

    public function delete(Request $request)
    {
        try {
            session()->now('success', 'All Deleted');
            ApiProduct::whereNotNull('sku')->delete();
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
        }
        return redirect()->route('statamic.cp.weareframework.api-product-importer.dashboard.index');
    }

}
