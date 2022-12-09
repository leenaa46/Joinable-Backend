<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends BaseModel
{
    use HasFactory;

    public $image_profile_collection_name = 'PROFILE';
    public $image_gallery_collection_name = 'GALLERY';

    protected $with = ['image_profile'];

    public function image_profile()
    {
        return $this->morphOne(config('media-library.media_model'), 'model')
            ->where('collection_name', $this->image_profile_collection_name);
    }

    public function image_galleries()
    {
        return $this->morphMany(config('media-library.media_model'), 'model')
            ->where('collection_name', $this->image_gallery_collection_name);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
