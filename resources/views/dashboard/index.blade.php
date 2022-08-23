@extends('statamic::layout')
@section('title', 'API Product Importer')

@section('content')
    <div class="flex items-center mb-3">
        <h1 class="flex-1">API Product Importer Dashboard</h1>
    </div>
    @if(!is_null($settings->values['api_product_importer_route']) && !empty($settings->values['api_product_importer_route']))
        <div class="mb-3">
            <a href="{{ cp_route('weareframework.api-product-importer.import.index') }}" class="btn-primary">Import</a>
        </div>
    @endif
        <div>
            <div class="mb-3">
                <p>You have {{ $apiProductsCount ?? 0 }} Products imported so far</p>
                @if(is_null($settings->values['api_product_importer_route']) || empty($settings->values['api_product_importer_route']))
                    <p class="font-bold">You need to fill out the importer route in settings</p>
                @endif

                <div class="card mt-2">
                    <p class="font-bold">Current settings:</p>
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
