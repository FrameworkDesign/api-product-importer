<?php

namespace Weareframework\ApiProductImporter\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Statamic\Facades\Site;
use Statamic\Facades\Collection;
use Statamic\Http\Controllers\CP\CpController;
use Weareframework\ApiProductImporter\Jobs\Import\ImportAllConfigurableApiProductsToStatamic;
use Weareframework\ApiProductImporter\Jobs\Import\ImportAllSimpleApiProductsToStatamic;
use Weareframework\ApiProductImporter\Models\ApiProduct;

class StatamicImportController extends CpController
{

    public function index(Request $request)
    {
        return 'Statamic import controller';
    }

    public function siteTarget(Request $request)
    {
        $collections = Collection::all()->map(function ($collection) {
            return [
                'label' => $collection->title(),
                'value' => $collection->handle(),
            ];
        })->sortBy('label')->values()->toArray();

        $collections2 = [
            [
                'label' => 'Products',
                'value' => 'products'
            ],
            [
                'label' => 'Products with Variants',
                'value' => 'products_with_variants'
            ]
        ];

        $sites = Site::all()->map(function (\Statamic\Sites\Site $site) {
            return [
                'label' => $site->name(),
                'value' => $site->handle(),
            ];
        })->sortBy('label')->values()->toArray();

        return view('api-product-importer::statamic-import.site-target', [
            'collections' => $collections,
            'sites' => $sites,
        ]);
    }

    public function fieldMapping(Request $request)
    {
        try {
            $savedMapping = cache()->get('api-product-statamic-saved-data-mapping');
            $handle = $request->get('collection');
            $type = 'configurable';//($handle === 'products_with_variants') ? 'configurable' : 'simple';
            $collection = Collection::findByHandle('products'); // $handle
            $blueprintHandle = ($handle === 'products_with_variants') ? 'products_with_variants' : 'products';
            /** @var \Statamic\Fields\Blueprint $blueprint */
            $blueprint = $collection->entryBlueprint($blueprintHandle);
            if (is_null($blueprint)) {
                throw new \Exception('No blueprint matches that name');
            }
            $fields = $blueprint->fields()->resolveFields()->toArray();

            $request->session()->put('api-product-statamic-data-import-type', $type);
            $request->session()->put('api-product-statamic-data-import-collection', $handle);
            $request->session()->put('api-product-statamic-data-import-site', request('site'));

            $keys = (new ApiProduct())->getFillableWithout();

            return view('api-product-importer::statamic-import.field-mapping', [
                'type' => $type,
                'keys' => $keys,
                'fields' => $fields,
                'savedMapping' => $savedMapping
            ]);
        } catch(\Exception $exception) {
            session()->flash('error', $exception->getMessage());
            return redirect()->back();
        }
    }

    public function processImport(Request $request)
    {
        try {
            cache()->forget("api-product-statamic-import-cleared");
            $mapping = collect($request->get('mapping'))->filter();
            $customMapping = ($request->has('custom_field_mapping')) ? collect($request->get('custom_field_mapping'))->filter() : collect([]);
            $collection = $request->session()->get('api-product-statamic-data-import-collection');
            Log::info('processImport: ' . $collection);
            $site = session()->get('api-product-statamic-data-import-site', Site::default()->handle());
            $uuid = Str::uuid()->toString();
            $request->session()->put('api-product-statamic-data-import-uuid', $uuid);

            cache()->put('api-product-statamic-saved-data-mapping', $mapping);

            $collectionModel = Collection::findByHandle('products');
            $blueprintHandle = ($collection === 'products_with_variants') ? 'products_with_variants' : null;
            /** @var \Statamic\Fields\Blueprint $blueprint */
            $blueprint = $collectionModel->entryBlueprint($blueprintHandle);

            if ($collection === 'products') {
                $request->session()->put('api-product-statamic-data-import-type', 'configurable');
                ImportAllSimpleApiProductsToStatamic::dispatch(
                    $uuid,
                    $site,
                    $collection,
                    $mapping,
                    $customMapping
                );
            }

            if ($collection === 'products_with_variants') {
                $request->session()->put('api-product-statamic-data-import-type', 'configurable');
                ImportAllConfigurableApiProductsToStatamic::dispatch(
                    $uuid,
                    $site,
                    $collection,
                    $mapping,
                    $customMapping
                );
            }

            $request->session()->forget('api-product-statamic-data-import-keys');
            $request->session()->forget('api-product-statamic-data-import-collection');
            $request->session()->forget('api-product-statamic-data-import-site');

            session()->flash('success', 'Running');

            return redirect(cp_route('weareframework.api-product-importer.statamic.finished'));
        } catch(\Exception $exception) {
            session()->flash('error', $exception->getMessage());
            return redirect()->back();
        }
    }

    public function finished(Request $request)
    {
        $type = $request->session()->get('api-product-statamic-data-import-type') ?? 'no type';
        $uuid = $request->session()->get('api-product-statamic-data-import-uuid') ?? null;
        return view('api-product-importer::statamic-import.finished', [
            'type' => $type,
            'uuid' => $uuid,
        ]);
    }

    public function clear(Request $request)
    {
        cache()->put("api-product-statamic-import-cleared", true);
        $uuid = $request->session()->get('api-product-statamic-data-import-uuid') ?? null;
        cache()->forget("{$uuid}-api-product-statamic-import-total");
        cache()->forget("{$uuid}-api-product-statamic-import-processed");
        cache()->forget("{$uuid}-api-product-statamic-import-errors");
        return redirect()->back();
    }



}
