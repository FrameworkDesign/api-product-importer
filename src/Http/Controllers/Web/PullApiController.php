<?php

namespace Weareframework\ApiProductImporter\Http\Controllers\Web;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Statamic\Facades\Collection;
use Statamic\Http\Controllers\CP\CpController;
use Weareframework\ApiProductImporter\Actions\Products\ImportApiProductAction;
use Weareframework\ApiProductImporter\Library\Errors\GeneralError;
use Weareframework\ApiProductImporter\Library\Files\File;
use Weareframework\ApiProductImporter\Library\Settings\CollectSettings;
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

    public function single(Request $request, File $file)
    {
        try {
            $sku = $request->input('sku');
            $settings = (new CollectSettings($file))->handle();
            $url = $settings->values['api_product_importer_products_single_route'];
            $url = str_replace('{sku}', $sku, $url);

            Log::info('$url '.$url);
            $response = Http::get($url);

            $apiProduct = null;

            if ($response->successful()) {
                // import the punks
                $importData = $response->json();

                if ($importData['success'] === false) {
                    throw new \Exception($importData['message']);
                }

                if (isset($importData['product'])) {
                    ImportApiProductAction::perform($importData['product']);
                }

                if (isset($importData['variants'])) {
                    $apiProducts = $importData['variants'];
                    foreach($apiProducts as $product) {
                        ImportApiProductAction::perform($product);
                    }
                }
            } else {
                throw new \Exception('It could not find the product');
            }

            if ($response->failed()) {
                throw new \Exception('It failed the pull');
            }

            $response->onError(function() {
                throw new \Exception('It did not pull');
            });
            session()->flash('success', 'Importing');
            return redirect()->route('statamic.cp.weareframework.api-product-importer.api-pulled-products.index');
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
            return redirect()->route('statamic.cp.weareframework.api-product-importer.api-pulled-products.index');
        }
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
