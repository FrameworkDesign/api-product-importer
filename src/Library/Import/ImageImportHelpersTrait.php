<?php

namespace  Weareframework\ApiProductImporter\Library\Import;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Statamic\Facades\AssetContainer;
use Statamic\Support\Arr;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Intervention\Image\ImageManagerStatic as InterventionImage;

trait ImageImportHelpersTrait
{

    private function importImages($mappedData)
    {
        if (!property_exists($this, 'blueprint') || !property_exists($this, 'mappedData')) {
            return $mappedData;
        }

        $fields = $this->blueprint->fields()->resolveFields()->toArray();
        return $mappedData->map(function ($item, $handle) use($fields) {
            return $this->checkForAssetFields($item, $handle, $fields);
        });
    }

    private function checkForAssetFields($item, $handle, $fields) {
        if (is_null($item)) {
            return $item;
        }

        $matches = Arr::where($fields, function ($value, $fKey) use($handle) {
            return $value['type'] === 'assets' && $value['handle'] === $handle;
        });

        if (count($matches) > 0) {
            $match = Arr::first($matches);
            if (is_array($item)) {
                $newArray = [];
                foreach($item as $single) {
                    $newArray[] = $this->importImage($match, $single, $handle);
                }

                $item = $newArray;
            } else {
                $item = $this->importImage($match, $item, $handle);
            }

        }

        return $item;
    }

    private function importImage($match, $item, $handle)
    {
        $assetUrl = $item;
        return $this->downloadAsset($match, $assetUrl ?? '', $this->collection);
    }

    private function downloadAsset($match, string $url = null, string $collection)
    {
        if (! $url) {
            return false;
        }

        try {
            $image = Http::retry(3, 500)->get($url)->body();
            $disk = config('statamic.api-product-importer.disk');
            $originalImageName = basename($url);
            $assetPath = "{$collection}/images/{$originalImageName}";
            $tempFile = 'temp';
            $success = $this->compressImage($url, 'test');

            if (!$success) {
                Storage::put($tempFile, $image);
            }

            $defaultContainer = config('statamic.api-product-importer.assets_container');
            $fieldContainer = (isset($match['container'])) ? $match['container'] : $defaultContainer;

            $assetContainer = AssetContainer::findByHandle($fieldContainer);
            $asset = $assetContainer->makeAsset($assetPath);

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
            Log::info('ImageImportHelpersTrait error: ' . $e->getMessage());
//            Log::info('ImageImportHelpersTrait error: ' . $e->getTraceAsString());
            return null;
        }
    }

    protected function compressImage($url)
    {
        $success = false;
        try {
            $extensionSave = config('statamic.api-product-importer.extension_save');
            $imageQualitySave = config('statamic.api-product-importer.image_quality_save') ?? 50;

            // $asset->width()
            $newTempFile = InterventionImage::make($url);

            if ($newTempFile->width() > config('statamic.api-product-importer.resize_pixels')) {
                // resize the image to a width of {resize_pixels} and constrain aspect ratio (auto height)
                $newTempFile->resize(config('statamic.api-product-importer.resize_pixels'), null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            $newTempFile->save('temp', $imageQualitySave, $extensionSave);

            Storage::put($tempFile = 'temp', $newTempFile);
            $success = true;
        } catch (\Exception $e) {
            Log::info('InterventionImage conversion failed error: ' . $e->getMessage());
        }

        return $success;
    }
}
