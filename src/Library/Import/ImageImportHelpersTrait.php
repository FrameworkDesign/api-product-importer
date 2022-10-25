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
            $tempFile = Str::random();
            $success = $this->compressImage($url, $tempFile);

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
            return null;
        }
    }

    protected function compressImage($url, $tempFile)
    {
        $success = false;
        try {
            $extensionSave = 'jpg';//config('statamic.api-product-importer.extension_save');
            $imageQualitySave = 50;//config('statamic.api-product-importer.image_quality_save') ?? 50;
            $newResize = config('statamic.api-product-importer.resize_pixels');
            // $asset->width()
            $newTempFile = InterventionImage::make($url);

            Log::info('extensionSave: ' . $extensionSave);
            Log::info('imageQualitySave: ' . $imageQualitySave);
            Log::info('newTempfile->width: ' . $newTempFile->width());
            Log::info('newResize: ' . $newResize);
            if ($newTempFile->width() > $newResize) {
                // resize the image to a width of {resize_pixels} and constrain aspect ratio (auto height)
                //$newTempFile->resize($newResize, null, function ($constraint) {
                //    $constraint->aspectRatio();
                //});
                //$newTempFile->resizeCanvas($newResize, null, 'center', false, '#ffffff');
                $jpg = InterventionImage::canvas($newTempFile->width(), $newTempFile->height(), '#ffffff');
                $jpg->insert($url);
                $jpg->resize($newResize, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
            $jpg->save('temp', $imageQualitySave, $extensionSave);
            Storage::put($tempFile, $jpg);
            //$newTempFile->save('temp', $imageQualitySave, $extensionSave);

            //Storage::put($tempFile, $newTempFile);
            $success = true;
        } catch (\Exception $e) {
            Log::info('InterventionImage conversion failed error: ' . $e->getMessage());
        }

        return $success;
    }
}
