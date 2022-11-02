@extends('statamic::layout')
@section('title', 'API Product Importer')

@section('content')
    @include('api-product-importer::partials.queue-info')
    <div class="flex items-center mb-3">
        <h1 class="flex-1 font-bold">API Product Importer Dashboard</h1>
        <p>The flow of getting products into statamic is like so:</p>
    </div>
    @if(isset($settings->values['api_product_importer_products_all_route']) && !is_null($settings->values['api_product_importer_products_all_route']) && !empty($settings->values['api_product_importer_products_all_route']))
        <div class="mb-5">
            <div>
                <h2 class="font-bold">1. API</h2>
                <p>We pull products from the RST Product portal</p>
                <a href="{{ cp_route('weareframework.api-product-importer.pull-data.index') }}" class="btn-primary">
                    Pull API Products
                </a>

                <a href="{{ cp_route('weareframework.api-product-importer.pull-data.delete') }}" class="btn-danger ml-2" onclick="return confirm('Are you sure you want to delete all api pulled products?');">
                    Delete All API Products
                </a>
                <br>
                <p class="mt-.5">You have <strong>{{ $apiParentProducts->count() }}</strong> API products imported so far</p>
                <p class="mt-.5"><strong>{{ $allApiProducts->count() }}</strong> API products and children/variants imported so far</p>

                <a href="{{ cp_route('weareframework.api-product-importer.api-pulled-products.index') }}" class="btn mt-1">
                    View imported API Products
                </a>

            </div>

            @if($apiParentProducts->count() > 0)
                <div class="mt-4">
                    <h2 class="font-bold">2. Import to Statamic</h2>
                    <p>Import API products into</p>
                    <a href="{{ cp_route('weareframework.api-product-importer.statamic.site-target') }}" class="btn-primary">Import to Statamic</a>
                </div>
            @endif

            @if($apiParentProducts->count() > 0)
                <div class="mt-4">
                    <h2 class="font-bold">3. Check Imported to Statamic</h2>
                    <p>Imported API products status and info</p>
                    <a href="{{ cp_route('weareframework.api-product-importer.statamic.finished') }}" class="btn-primary">Imported</a>
                </div>
            @endif
        </div>
    @endif
        <div>
            <div class="mb-3">

                @if(isset($settings->values['api_product_importer_products_all_route']) && is_null($settings->values['api_product_importer_products_all_route']) || empty($settings->values['api_product_importer_products_all_route']))
                    <p class="font-bold">You need to fill out the importer route in settings</p>
                @endif

                <div class="card mt-2">
                    <p class="font-bold">Current API settings:</p>
                    <table class="data-table mb-2">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Route</td>
                                <td>{{ $settings->values['api_product_importer_products_all_route'] ?? null }}</td>
                            </tr>
                            <tr>
                                <td>Method</td>
                                <td>{{ $settings->values['api_product_importer_products_all_method'] ?? null }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <a href="{{ cp_route('weareframework.api-product-importer.settings.index') }}" class="btn">Edit Settings</a>
                </div>
            </div>
        </div>
@stop
