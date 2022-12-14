@extends('statamic::layout')
@section('title', 'API Product Importer')

@section('content')
    <form action="{{ cp_route('weareframework.api-product-importer.statamic.process-import') }}" method="POST">
        {{ csrf_field() }}

        <header class="mb-3">
            <h1 class="mb-1">Map your data</h1>
            <div class="flex items-center justify-between">
                <a href="{{ cp_route('weareframework.api-product-importer.statamic.site-target') }}" class="btn">
                    Back
                </a>
                <button class="btn-primary">Import</button>
            </div>
        </header>

        <div class="card rounded p-3 lg:px-7 lg:py-5 shadow bg-white">
            <header class="text-center mb-6">
                <h1 class="mb-3">Map your data</h1>
                <p class="text-grey mb-2">This will import <strong>{{ $type }}</strong> products @if($type === 'configurable') and all children products associated with the parent product (configurable) @endif</p>
                <p class="text-grey mb-2">Match your data with the fields of the collection.</p>
            </header>
            <h2 class="mb-3">Fieldset Data</h2>
            <map-collection-fields-panel
                class="-mx-2"
                name="mapping"
                id="mapping"
                :savedMapping="{{ json_encode($savedMapping) }}"
                :config="{
                    keys: {{ json_encode($keys) }},
                    fields: {{ json_encode($fields) }},
                }"
            ></map-collection-fields-panel>
            @foreach ($errors as $error)
                <p class="text-red-500 mb-1">{{ $error }}</p>
            @endforeach
        </div>
    </form>
@endsection
