<?php

namespace Weareframework\ApiProductImporter\Jobs\Import;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\SimpleExcel\SimpleExcelReader;
use Statamic\Assets\Asset;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Collection as CollectionFacade;
use Statamic\Facades\Entry;
use Statamic\Facades\File;
use Statamic\Facades\Stache;
use Statamic\Support\Arr;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Weareframework\ApiProductImporter\Library\Import\ImageImportHelpersTrait;
use Weareframework\ApiProductImporter\Models\ApiProduct;

class ImportSimpleApiProductToStatamic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, ImageImportHelpersTrait;

    /** @var string */
    private $uuid;

    private $apiProduct;

    /** @var string */
    private $site;

    /** @var \Statamic\Entries\Collection */
    private $collection;

    /** @var \Illuminate\Support\Collection */
    private $mapping;
    private $mappedData;

    private $blueprint;

    private $index;
    private $isLast;

    private $errors;
    private $failedRows;

    public function __construct(
        string $uuid,
        $apiProduct,
        $index,
        string $site,
        string $collection,
        Collection $mapping,
        $isLast
    ) {
        $this->uuid = $uuid;
        $this->apiProduct = ApiProduct::find($apiProduct);
        $this->index = $index;
        $this->site = $site;
        $this->collection = CollectionFacade::findByHandle($collection);
        $this->mapping = $mapping;
        $this->isLast = $isLast;
        $this->errors = cache()->get("{$this->uuid}-api-product-statamic-import-errors");
        $this->failedRows = cache()->get("{$this->uuid}-api-product-statamic-import-failed");

        $blueprintHandle = ($collection === 'products_with_variants') ? 'products_with_variants' : null;
        /** @var \Statamic\Fields\Blueprint $blueprint */
        $this->blueprint = $this->collection->entryBlueprint($blueprintHandle);
    }

    public function handle()
    {
        Log::info('ImportSimpleApiProductToStatamic->handle ' . $this->index);
        try {
            $row = $this->apiProduct;

            Log::info('ImportSimpleApiProductToStatamic $this->apiProduct ' . json_encode($this->apiProduct));
            Log::info('ImportSimpleApiProductToStatamic $this->mapping ' . json_encode($this->mapping->toArray()));

            $this->mappedData = $this->mapping->map(function (string $rowKey) use ($row) {
                $value = $row[$rowKey] ?? null;
                return $value;
            });

            if (method_exists($this, 'importImages') && config('statamic.api-product-importer.download_images')) {
                $this->importImages();
            }

            Log::info('ImportSimpleApiProductToStatamic $mappedData ' . json_encode($this->mappedData->toArray()));

            $title = $this->mappedData->get('title');
            if (! $title) {
                $message = "[Row {$this->index}]: This row has no title.";
                $this->failedRows[] = $row;
                $this->errors[] = $message;
                throw new \Exception($message);
            }

            $type = 'create';

            $productExists = Entry::query()
                ->where('title', $title)
                ->where('site', $this->site)
                ->get();

            $entry = null;

            if ($productExists->count() === 0) {
                $entry = Entry::make()
                    ->slug(Str::slug($this->mappedData->get('title')))
                    ->locale($this->site)
                    ->collection($this->collection)
                    ->data(Arr::removeNullValues($this->mappedData->all()));

                $dateToUse = ($this->mappedData->has('date')) ? $this->mappedData->get('date', now()) : now();
                $entry->date($dateToUse);

                if (!$entry->save()) {
                    $this->failedRows[] = $row;
                    $this->errors[] = "[Row {$this->index}]: This new product could not save.";
                }
            }

            if ($productExists->count() > 0) {
                $type = 'update';
                $entry = $productExists->first()->data(Arr::removeNullValues($this->mappedData->all()));
                if (!$entry->save()) {
                    $failedRows[] = $row;
                    $errors[] = "[Row {$this->index}]: This updated product could not save";
                }
            }

            Log::info($type . ' - site: ' . $this->site . ' - collection:' . $this->collection . ' - title: ' . $title);

            cache()->increment("{$this->uuid}-api-product-statamic-import-processed");
            cache()->put("{$this->uuid}-api-product-statamic-import-errors", $this->errors);
            cache()->put("{$this->uuid}-api-product-statamic-import-failed", $this->failedRows);

            if ($this->isLast) {
                cache()->put("{$this->uuid}-api-product-statamic-import-finished", true);
            }

            Log::info('ImportSimpleApiProductToStatamic->handle FINISH ' . $this->index);
            return;
        } catch(\Exception $exception) {
            Log::info('ImportSimpleApiProductToStatamic error ' . $exception->getMessage());

            cache()->put("{$this->uuid}-api-product-statamic-import-errors", $this->errors);
            cache()->put("{$this->uuid}-api-product-statamic-import-failed", $this->failedRows);
        }
    }




}
