<?php

namespace  Weareframework\ApiProductImporter\Library\Import;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Statamic\Facades\AssetContainer;
use Statamic\Support\Arr;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait ImageImportHelpersTrait
{

    private function importImages(): void
    {
        if (! property_exists($this, 'blueprint') || !  property_exists($this, 'mappedData')) {
            return;
        }

        $fields = $this->blueprint->fields()->resolveFields()->toArray();

        $this->mappedData = $this->mappedData->map(function ($item, $handle) use($fields) {
            if (is_null($item)) {
                return $item;
            }

            $matches = Arr::where($fields, function ($value, $fKey) use($handle) {
                return $value['type'] === 'assets' && $value['handle'] === $handle;
            });


            if (count($matches) > 0) {
                $match = Arr::first($matches);
                $item = $this->importImage($match, $item, $handle);
            }

            return $item;
        });

    }

    private function importImage($match, $item, $handle)
    {
        return $this->downloadAsset($assetUrl ?? '', $this->collection);
    }

    private function downloadAsset(string $url = null, string $collection)
    {
        if (! $url) {
            return false;
        }

        try {
            $image = Http::retry(3, 500)->get($url)->body();

            $originalImageName = basename($url);

            Storage::put($tempFile = 'temp', $image);

            $assetContainer = AssetContainer::findByHandle(config('statamic.api-product-importer.assets_container'));

            $asset = $assetContainer->makeAsset("{$collection}/images/{$originalImageName}");

            if ($asset->exists() && config('statamic.api-product-importer.skip_existing_images')) {
                return $asset;
            }

            if ($asset->exists() && config('statamic.api-product-importer.overwrite_images')) {
                $asset->delete();
            }

            $asset->upload(
                new UploadedFile(
                    Storage::path($tempFile),
                    $originalImageName,
                )
            );

            $asset->save();

            return $asset->path();
        } catch (\Exception $e) {
            logger('Image download failed: ' . $e->getMessage());
            return null;
        }
    }
}
