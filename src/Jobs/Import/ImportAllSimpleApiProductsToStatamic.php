<?php

namespace Weareframework\ApiProductImporter\Jobs\Import;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\SimpleExcel\SimpleExcelReader;
use Statamic\Facades\Collection as CollectionFacade;
use Statamic\Facades\Entry;
use Statamic\Facades\File;
use Statamic\Facades\Stache;
use Statamic\Support\Arr;
use Weareframework\ApiProductImporter\Jobs\Import\ImportSimpleApiProductToStatamic;
use Weareframework\ApiProductImporter\Models\ApiProduct;

class ImportAllSimpleApiProductsToStatamic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var string */
    private $uuid;
    /** @var string */
    private $site;
    /** @var \Statamic\Entries\Collection */
    private $collection;
    /** @var \Illuminate\Support\Collection */
    private $mapping;

    public function __construct(
        string $uuid,
        string $site,
        string $collection,
        Collection $mapping
    ) {
        $this->uuid = $uuid;
        $this->site = $site;
        $this->collection = $collection;
        $this->mapping = $mapping;
    }

    public function handle()
    {
        $apiProducts = ApiProduct::whereNull('parent_sku')->where('type', 'simple')->get();
        $apiProductsCount = $apiProducts->count();
        Log::info('ImportAllSimpleApiProductsToStatamic Running - products: ' . $apiProductsCount);

        cache()->put("{$this->uuid}-api-product-statamic-import-total", $apiProductsCount);
        cache()->put("{$this->uuid}-api-product-statamic-import-processed", 0);
        cache()->put("{$this->uuid}-api-product-statamic-import-finished", false);

        $failedRows = [];
        $errors = [];

        cache()->put("{$this->uuid}-api-product-statamic-import-errors", $errors);
        cache()->put("{$this->uuid}-api-product-statamic-import-failed", $failedRows);

        $apiProducts->each(function ($apiProduct, $index) use($apiProductsCount) {
            Log::info('item: ' . $apiProduct->id);
            $isLast = ($index === ($apiProductsCount - 1));
            ImportSimpleApiProductToStatamic::dispatch(
                $this->uuid,
                $apiProduct->id,
                $index,
                $this->site,
                $this->collection,
                $this->mapping,
                $isLast
            );
        });
    }
}