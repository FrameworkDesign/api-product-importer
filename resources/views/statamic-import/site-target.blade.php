@extends('statamic::layout')
@section('title', 'API Product Importer')

@section('content')
    <form action="{{ cp_route('weareframework.api-product-importer.statamic.field-mapping') }}" method="post">
        {{ csrf_field() }}

        <header class="mb-3">
            <h1 class="mb-1">Select target</h1>
            <div class="flex items-center justify-between">
                <a href="{{ cp_route('weareframework.api-product-importer.dashboard.index') }}" class="btn">
                    Back
                </a>
                <button class="btn-primary">Continue</button>
            </div>
        </header>

        <div class="card rounded p-3 lg:px-7 lg:py-5 shadow bg-white">
            <header class="text-center mb-6">
                <h1 class="mb-3">Select target</h1>
                <p class="text-grey">Select a target collection to where the data must be imported.</p>
            </header>

            <input type="hidden" ref="collection" name="collection">
            <input type="hidden" ref="site" name="site">
            <div class="flex gap-2">
                <div class="w-1/2">
                    <label class="font-bold text-base mb-sm">Site</label>
                    <v-select
                        class="w-full"
                        :options="{{ json_encode($sites) }}"
                        :reduce="selection => selection.value"
                        @input="(value) => this.$refs.site.value = value"
                    />
                </div>
                <div class="w-1/2">
                    <label class="font-bold text-base mb-sm">Collection</label>
                    <v-select
                        class="w-full"
                        :options="{{ json_encode($collections) }}"
                        :reduce="selection => selection.value"
                        @input="(value) => this.$refs.collection.value = value"
                    />
                </div>
            </div>
        </div>
    </form>
@endsection
