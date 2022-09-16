@extends('statamic::layout')
@section('title', 'API Product Importer')

@section('content')
    <div class="flex items-center mb-3">
        <h1 class="flex-1 font-bold">API Product Importer Dashboard</h1>
        <p>The flow of getting products into statamic is like so:</p>
    </div>
    @if(isset($settings->values['api_product_importer_route']) && !is_null($settings->values['api_product_importer_route']) && !empty($settings->values['api_product_importer_route']))
        <div class="mb-5">
            <div>
                <h3 class="font-bold">1. API</h3>
                <p>We pull products from the RST Product portal</p>
                <a href="{{ cp_route('weareframework.api-product-importer.pull-data.index') }}" class="btn-primary">
                    Pull API Products
                </a>

                <a href="{{ cp_route('weareframework.api-product-importer.pull-data.delete') }}" class="btn-danger ml-2" onclick="return confirm('Are you sure you want to delete all api pulled products?');">
                    Delete All API Products
                </a>
                <br>
                <p class="mt-.5">You have <strong>{{ $apiProducts->count() }}</strong> API products imported so far</p>
            </div>

            @if($apiProducts->count() > 0)
                <div class="mt-2">
                    <h3 class="font-bold">2. Import to Statamic</h3>
                    <p>Import API products into</p>
                    <a href="{{ cp_route('weareframework.api-product-importer.statamic.site-target') }}" class="btn-primary">Import to Statamic</a>
                </div>
            @endif

            @if($apiProducts->count() > 0)
                <div class="mt-2">
                    <h3 class="font-bold">3. Check Imported to Statamic</h3>
                    <p>Imported API products status and info</p>
                    <a href="{{ cp_route('weareframework.api-product-importer.statamic.finished') }}" class="btn-primary">Imported</a>
                </div>
            @endif
        </div>
    @endif
        <div>
            <div class="mb-3">

                @if(isset($settings->values['api_product_importer_route']) && is_null($settings->values['api_product_importer_route']) || empty($settings->values['api_product_importer_route']))
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
                                <td>{{ $settings->values['api_product_importer_route'] ?? null }}</td>
                            </tr>
                            <tr>
                                <td>Method</td>
                                <td>{{ $settings->values['api_product_importer_method'] ?? null }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <a href="{{ cp_route('weareframework.api-product-importer.settings.index') }}" class="btn">Edit Settings</a>
                </div>
            </div>
        </div>
@stop
