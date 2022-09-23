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

    private function importImages(): void
    {
        if (!property_exists($this, 'blueprint') || !property_exists($this, 'mappedData')) {
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
        });

    }

    private function importImage($match, $item, $handle)
    {
        $assetUrl = $item;
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
            Log::info('image: ' . json_encode($image) . ' $url: ' . $url);
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

            Log::info('asset->width() ' . $asset->width() . ' ' . config('statamic.api-product-importer.resize_pixels'));
            if ($asset->width() > config('statamic.api-product-importer.resize_pixels')) {
                $extensionSave = config('statamic.api-product-importer.extension_save');
                $imageQualitySave = config('statamic.api-product-importer.image_quality_save') ?? 50;

                // and you are ready to go ...
                // resize the image to a width of {resize_pixels} and constrain aspect ratio (auto height)
                $newTempFile = InterventionImage::make($asset->resolvedPath())->resize(config('statamic.api-product-importer.resize_pixels'), null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($asset->resolvedPath(), $imageQualitySave, ($extensionSave === 'none') ? $asset->extension() : $extensionSave);
                $asset->extension('jpg');
                Log::info(json_encode($newTempFile));
            }

            if(class_exists('Imagick')) {
                $output = '';
                $imagick = new \Imagick($asset->resolvedPath());
                $bytes = $imagick->getImageBlob();
                $output .= "Image byte size before stripping: " . strlen($bytes) . "<br/>";
                $imagick->stripImage();
                $bytes = $imagick->getImageBlob();
                $output .= "Image byte size after stripping: " . strlen($bytes) . "<br/>";

                Log::info('$output: ' . $output);
            }

            $asset->save();

            return $asset->path();
        } catch (\Exception $e) {
            Log::info('ImageImportHelpersTrait error: ' . $e->getMessage());
            Log::info('ImageImportHelpersTrait error: ' . $e->getTraceAsString());
            return null;
        }
    }
}
