@extends('statamic::layout')
@section('title', 'API Product Importer')

@php
    $status = !is_null(cache("{$uuid}-api-product-statamic-import-total")) && cache("{$uuid}-api-product-statamic-import-total") === cache("{$uuid}-api-product-statamic-import-processed") ? 'done' : 'ongoing';
    if(! is_null(cache("api-product-statamic-import-cleared"))) {
        $status = 'cleared';
    }
@endphp
@section('content')
    <form action="{{ cp_route('weareframework.api-product-importer.statamic.finished') }}" method="POST">
        {{ csrf_field() }}

        <header class="mb-3">
            <h1>Imported to statatmic {{ $status }} - type: {{ $type ?? 'no type' }}</h1>
            <p><strong>uuid:</strong> {{ $uuid }}</p>
        </header>

        <div class="card rounded p-3 lg:px-7 lg:py-5 shadow bg-white">
            <header class="text-center">
                <h1 class="mb-3">Import to statatmic {{ $status }} - type: {{ $type ?? 'no type' }}</h1>
                @if($status === 'cleared')
                @elseif (is_null(cache("{$uuid}-api-product-statamic-import-total")))
                    <p class="text-grey">Your import is being processed...</p>
                @else
                    <p class="text-grey">From <strong>{{ cache("{$uuid}-api-product-statamic-import-total") }}</strong> uploaded rows of data <strong>{{ cache("{$uuid}-statamic-import-processed") }}</strong> rows have been imported.</p>
                @endif
                @if ($status === 'ongoing')
                    <button type="button" class="btn-primary mt-2" onclick="window.location.reload()">Refresh</button>
                @endif
            </header>

            <div class="text-center">
                @if($status !== 'cleared')
                    <a href="{{ cp_route('weareframework.api-product-importer.statamic.clear') }}" class="btn-default mt-2">Clear Import data</a>
                @endif
            </div>

            @if (count(cache("{$uuid}-statamic-import-errors") ?? []))
                <div class="mb-4 mt-6">
                    <h2 class="mb-1">Errors</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Message</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (cache("{$uuid}-api-product-statamic-import-errors") as $index => $error)
                                <tr>
                                    <td class="text-red mb-1">{{ $error }}</td>
                                    <td class="text-red mb-1">{{ implode (", ", cache("{$uuid}-api-product-statamic-import-failed")[$index]) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="mt-5">
            <a href="{{ cp_route('weareframework.api-product-importer.dashboard.index') }}" class="btn-primary">
                Back to Dashboard
            </a>
            <a href="{{ cp_route('weareframework.api-product-importer.statamic.site-target') }}" class="ml-2 text-white btn-primary">
                Import Again
            </a>
        </div>
    </form>
@endsection
