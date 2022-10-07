@extends('statamic::layout')
@section('title', 'Done < API Imported')

@section('content')
        <header class="flex flex-row items-start justify-between mb-3">
            <h1>API Imported</h1>
            <a href="{{ cp_route('weareframework.api-product-importer.dashboard.index') }}" class="btn">Dashboard</a>
        </header>

        <div class="card rounded p-3 lg:px-7 lg:py-5 shadow bg-white">

            <header class="text-center">
                <h1 class="mb-1">API Imported Products</h1>
                <h3 class="mb-1">Actions</h3>
                <div class="flex justify-center items-center">
                    <a href="{{ cp_route('weareframework.api-product-importer.pull-data.delete') }}"
                       class="btn-danger btn-sm mr-1 mb-1"
                       onclick="return confirm('Are you sure you want to delete all api pulled products?');"
                    >Delete All products</a>
                    <a href="{{ cp_route('weareframework.api-product-importer.pull-data.index') }}" class="btn-primary btn-sm mr-1 mb-1">
                        Pull API Products
                    </a>
                    <a href="{{ cp_route('weareframework.api-product-importer.statamic.site-target') }}" class="btn-flat btn-sm mb-1">Import to Statamic</a>
                </div>

                <div class="flex justify-between">
                    <div class="text-center">
                        <h3 class="mb-1">Search</h3>
                        <form class="global-search mx-auto" style="max-width:250px;" action="{{ cp_route('weareframework.api-product-importer.api-pulled-products.index') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="state-container w-4 h-4 text-grey-50">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="9.813" cy="9.812" r="9.063" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" transform="rotate(-23.025 9.813 9.812)"></circle><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m16.221 16.22 7.029 7.03"></path></svg>
                            </div>
                            <label for="global-search" class="sr-only">Search</label>
                            <input type="text" autocomplete="off" name="query" id="query" value="{{ old('query', request()->input('query')) }}" placeholder="Search..." tabindex="-1" class="search-input">
                            <button type="submit" style="width: 22px;"><svg style="margin-top: 5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M.752 2.251a1.5 1.5 0 0 1 1.5-1.5m0 22.5a1.5 1.5 0 0 1-1.5-1.5m22.5 0a1.5 1.5 0 0 1-1.5 1.5m0-22.5a1.5 1.5 0 0 1 1.5 1.5m0 15.75v-1.5m0-3.75v-1.5m0-3.75v-1.5m-22.5 12v-1.5m0-3.75v-1.5m0-3.75v-1.5m5.25-5.25h1.5m3.75 0h1.5m3.75 0h1.5m-12 22.5h1.5m3.75 0h1.5m3.75 0h1.5m-6-5.25v-12m4.5 4.5-4.5-4.5-4.5 4.5"></path></svg></button>
                        </form>
                    </div>

                    <div class="text-center">
                        <h3 class="mb-1">Import Product (By SKU)</h3>
                        <form class="global-search mx-auto" style="max-width:250px;" action="{{ cp_route('weareframework.api-product-importer.pull-data.single.sku') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="state-container w-4 h-4 text-grey-50">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="9.813" cy="9.812" r="9.063" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" transform="rotate(-23.025 9.813 9.812)"></circle><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m16.221 16.22 7.029 7.03"></path></svg>
                            </div>
                            <label for="global-search" class="sr-only">Import SKU</label>
                            <input type="text" autocomplete="off" name="sku" id="sku" value="{{ old('sku', request()->input('sku')) }}" placeholder="Search..." tabindex="-1" class="search-input">
                            <button type="submit" style="width: 22px;"><svg style="margin-top: 5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M.752 2.251a1.5 1.5 0 0 1 1.5-1.5m0 22.5a1.5 1.5 0 0 1-1.5-1.5m22.5 0a1.5 1.5 0 0 1-1.5 1.5m0-22.5a1.5 1.5 0 0 1 1.5 1.5m0 15.75v-1.5m0-3.75v-1.5m0-3.75v-1.5m-22.5 12v-1.5m0-3.75v-1.5m0-3.75v-1.5m5.25-5.25h1.5m3.75 0h1.5m3.75 0h1.5m-12 22.5h1.5m3.75 0h1.5m3.75 0h1.5m-6-5.25v-12m4.5 4.5-4.5-4.5-4.5 4.5"></path></svg></button>
                        </form>
                    </div>
                </div>
            </header>

            @if ($products->count() > 0)
                <div class=" mt-6">
                    <h2 class="mb-1">Products</h2>
                    <table class="data-table mb-4">
                        <thead>
                            <tr>
                                <th>index</th>
                                <th>SKU</th>
                                <th>Name</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $index => $fwkProduct)
                                <tr>
                                    <td class="text-black mb-1" style="word-break: break-word;">{!! $index !!}</td>
                                    <td class="text-black mb-1" style="word-break: break-word;">{!! $fwkProduct->sku ?? 'no sku' !!}</td>
                                    <td class="text-black mb-1" style="word-break: break-word;">{!! $fwkProduct->name ?? '' !!}</td>
                                    <td>
                                        <push-api-product-into-statamic-helper
                                            :sku="{{ $fwkProduct->sku }}"
                                            :sites="{{ json_encode($sites) }}"
                                            :collections="{{ json_encode($collections) }}"
                                        ></push-api-product-into-statamic-helper>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $products->links() }}
                </div>
            @endif
        </div>
@endsection
