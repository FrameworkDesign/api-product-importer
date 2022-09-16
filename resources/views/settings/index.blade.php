@extends('statamic::layout')
@section('title', 'API Product Importer')
@section('content')
    <div class="flex items-center mb-3">
        <h1 class="flex-1">API Product Importer Settings</h1>
    </div>
    <div class="flex flex-col items-start justify-start mb-3">
        <p class="mb-1">Control your Product Importer general settings here. Make sure to read the instructions on each input.</p>
        <a href="{{ cp_route('weareframework.api-product-importer.dashboard.index') }}" class="btn">Dashboard</a>
    </div>

    <div>
        <publish-form
            title="Settings"
            action="{{ cp_route('weareframework.api-product-importer.settings.update') }}"
            :blueprint='@json($blueprint)'
            :meta='@json($meta)'
            :values='@json($values)'
        />

    </div>
@stop
