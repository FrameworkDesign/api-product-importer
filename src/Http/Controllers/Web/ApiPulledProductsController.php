<?php

namespace Weareframework\ApiProductImporter\Http\Controllers\Web;

use Illuminate\Http\Request;
use Statamic\Facades\Collection;
use Statamic\Facades\Site;
use Statamic\Http\Controllers\CP\CpController;
use Weareframework\ApiProductImporter\Models\ApiProduct;

class ApiPulledProductsController extends CpController
{
    public function index(Request $request)
    {
        $collections = Collection::all()->map(function ($collection) {
            return [
                'label' => $collection->title(),
                'value' => $collection->handle(),
            ];
        })->sortBy('label')->values()->toArray();

        $sites = Site::all()->map(function (\Statamic\Sites\Site $site) {
            return [
                'label' => $site->name(),
                'value' => $site->handle(),
            ];
        })->sortBy('label')->values()->toArray();

        $query = ApiProduct::query();

        $searchQuery = $request->get('query') ?? null;
        if (! is_null($searchQuery)) {
            $query = $query->where('sku', $searchQuery)
                ->orWhere('name', $searchQuery);
        }

        $products = $query->whereNull('parent_sku')->paginate(40);

        return view('api-product-importer::api-imported.index', [
            'products' => $products,
            'collections' => $collections,
            'sites' => $sites
        ]);
    }
}
