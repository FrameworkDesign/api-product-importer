<?php

namespace Weareframework\ApiProductImporter\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Statamic\Facades\Collection;
use Statamic\Facades\Config;
use Statamic\Support\Arr;
use Statamic\Facades\Site;
use Illuminate\Http\Request;
use Statamic\Facades\Blueprint;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\UploadedFile;
use Statamic\Http\Controllers\CP\CpController;
use Weareframework\ApiProductImporter\Actions\Products\ImportApiProductAction;
use Weareframework\ApiProductImporter\Jobs\Pull\ImportApiProduct;
use Weareframework\ApiProductImporter\Library\Errors\GeneralError;
use Weareframework\ApiProductImporter\Library\Settings\CollectSettings;
use Weareframework\ApiProductImporter\Models\ApiProduct;
use Weareframework\ApiProductImporter\Library\Files\File;

class ApiImportController extends CpController
{

    public function index(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'index',
                'data' => null
            ]);
        } catch (\Exception $exception) {
            return GeneralError::api($exception);
        }
    }

    public function pull($sku, Request $request, File $file)
    {
        try {
            $settings = (new CollectSettings($file))->handle();
            $url = $settings->values['api_product_importer_products_single_route'];
            $url = str_replace('{sku}', $sku, $url);

            Log::info('$url '.$url);
            $response = Http::get($url);

            $apiProduct = null;

            if ($response->successful()) {
                // import the punks
                $importData = $response->json();
                if(isset($importData['product'])) {
                    $apiProduct = $importData['product'];
                    ImportApiProductAction::perform($importData['product']);
                }
            }

            if ($response->failed()) {
                throw new \Exception('It failed the pull');
            }

            $response->onError(function() {
                throw new \Exception('It did not pull');
            });

            $collection = Collection::findByHandle('products'); // $handle
            $blueprintHandle = 'products';
            /** @var \Statamic\Fields\Blueprint $blueprint */
            $blueprint = $collection->entryBlueprint($blueprintHandle);
            $keys = (new ApiProduct())->getFillableWithout();
            $fields = $blueprint->fields()->resolveFields()->toArray();

            return response()->json([
                'success' => true,
                'message' => 'index',
                'product' => $apiProduct,
                'mapping' => [
                    'keys' => $keys,
                    'fields' => $fields
                ]
            ]);
        } catch (\Exception $exception) {
            return GeneralError::api($exception);
        }
    }

    public function map(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'map',
                'data' => null
            ]);
        } catch (\Exception $exception) {
            return GeneralError::api($exception);
        }
    }

    public function store(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'store',
                'data' => null
            ]);
        } catch (\Exception $exception) {
            return GeneralError::api($exception);
        }
    }

}
