<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasAssetsTrait
{
    public static function bootHasAssetsTrait()
    {
        
            static::deleted(function (self $self) {
                $self->deleteAssets($self);
            });
    
            if (in_array(SoftDeletes::class, class_uses(static::class))) {
                static::restored(function (self $self) {
                    $self->restoreAssets($self);
                });
            }
    
            static::saving(function ($model) {
            
                $model->saveAssets($model, request());
            });
        
       
    }

    private function saveAsset($model, Request $request, string $key, string $uploadPath)
    {
        $new = $request->{$key};


        $old = @$model->getOriginal()[$key];

        if (!empty($old) && $new && is_file($new)) {
            $path = preg_replace("/[\s\S]*storage/", '', $old);
            Storage::disk('public')->delete($path);
        }

       if ($new && is_file($new)) {
            $path = $request->file("$key")->storePublicly($uploadPath, "public");

            $image =  "/storage/" . $path;
            $model->setAttribute($key, $image);
        }
    }

    public function deleteAsset(Model $model, string $propertyName)
    {
        foreach ($model->{$propertyName} as $key) {
            if (property_exists($model, $propertyName))
                $path = preg_replace("/[\s\S]*storage/", "", @$model->getOriginal()[$key]);

            Storage::disk('public')->delete($path);
        }
    }

    public  function saveAssets($model, Request $request): void
    {
        $uploadPath = (string) Str::of(class_basename($model))->lower()->plural();
        //TODO:: how we can save array of images by one request
        if (property_exists($model, "assets")) {
            foreach ($model->assets as $key) {

                $this->saveAsset($model, $request, $key, "/images/" . $uploadPath);
            }
        }

        if (property_exists($model, "files")) {
            foreach ($model->files as $key) {
                $this->saveAsset($model, $request, $key, "/files/" . $uploadPath);
            }
        }
    }

    private function deleteAssets($model)
    {
        if (property_exists($model, "assets")) {
            $this->deleteAsset($model, "assets");
        }

        if (property_exists($model, "files")) {
            $this->deleteAsset($model, "files");
        }
    }
}
