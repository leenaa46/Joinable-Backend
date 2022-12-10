<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostContent extends BaseModel
{
    use HasFactory;

    public $image_content_collection_name = 'CONTENT';

    protected $with = ['image_content'];

    public function image_content()
    {
        return $this->morphOne(config('media-library.media_model'), 'model')
            ->where('collection_name', $this->image_content_collection_name);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
