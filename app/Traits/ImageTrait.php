<?php

namespace App\Traits;

use Spatie\Image\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;

trait ImageTrait
{
    /**
     * Save Image.
     *
     * @param Model $model
     * @param string $collectionName
     */
    public function addFileToModel($image, Model $model, string $collectionName)
    {
        if (\is_string($image)) {
            return $this->addUrlToModel($image, $model, $collectionName);
        }

        $path = storage_path('tmp/uploads');

        $file = $image;
        $name = uniqid() . '_' . trim($file->getClientOriginalName());
        $file->move($path, $name);
        Image::load($path . '/' . $name)->width(1024)->height(1024)->save();

        return $model->addMedia(storage_path('tmp/uploads/' . $name))
            ->toMediaCollection($collectionName);
    }

    /**
     * Save Image.
     *
     * @param Model $model
     * @param string $collectionName
     */
    public function addToModel(string | UploadedFile $image, Model $model, string $collectionName)
    {
        if (\filter_var($image, FILTER_VALIDATE_URL)) {
            return $model->addMediaFromUrl($image)->toMediaCollection($collectionName);
        }

        return $this->addFileToModel($image,  $model,  $collectionName);
    }
}
