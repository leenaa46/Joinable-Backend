<?php

namespace App\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;


class Media extends SpatieMedia
{
    protected $hidden = [
        "id",
        "model_type",
        "model_id",
        // "uuid",
        // "name",
        "file_name",
        "mime_type",
        "disk",
        "conversions_disk",
        "manipulations",
        "custom_properties",
        "generated_conversions",
        "responsive_images",
        "order_column",
        "created_at",
        "updated_at",
        "preview_url",
        // "collection_name"
    ];

    protected $appends = [
        "url",
        "preview"
    ];

    public function getUrlAttribute()
    {
        return $this->original_url;
    }

    public function getPreviewAttribute()
    {
        return $this->getUrl('preview');
    }
}
