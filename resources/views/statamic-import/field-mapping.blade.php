@extends('statamic::layout')
@section('title', 'API Product Importer')

@section('content')
    <form action="{{ cp_route('weareframework.api-product-importer.statamic.process-import') }}" method="POST">
        {{ csrf_field() }}

        <header class="mb-3">
            <div class="flex items-center justify-between">
                <h1>Map your data</h1>
                <button class="btn-primary">Import</button>
            </div>
        </header>

        <div class="card rounded p-3 lg:px-7 lg:py-5 shadow bg-white">
            <header class="text-center mb-6">
                <h1 class="mb-3">Map your data</h1>
                <p class="text-grey mb-2">This will import {{ $type }} products @if($type === 'configurable') and all children products associated with the parent product (configurable) @endif</p>
                <p class="text-grey mb-2">Match your data with the fields of the collection.</p>
            </header>
            <h2 class="mb-3">Fieldset Data</h2>
            <fwk-data-import
                name="mapping"
                id="mapping"
                :config="{
                    keys: {{ json_encode($keys) }},
                    fields: {{ json_encode($fields) }},
                }"
            ></fwk-data-import>
            @foreach ($errors as $error)
                <p class="text-red-500 mb-1">{{ $error }}</p>
            @endforeach
        </div>
    </form>
@endsection
