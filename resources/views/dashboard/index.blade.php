@extends('statamic::layout')
@section('title', 'API Product Importer')

@section('content')
    <div class="flex items-center mb-3">
        <h1 class="flex-1">API Product Importer Dashboard</h1>
    </div>
    <div class="mb-3">
        <a href="{{ cp_route('weareframework.api-product-importer.import.index') }}" class="btn-primary">Import</a>
    </div>
        <div>
            <div class="mb-3">
                <p>You have {{ $apiProductsCount ?? 0 }} Products imported so far</p>
            </div>
        </div>
@stop
