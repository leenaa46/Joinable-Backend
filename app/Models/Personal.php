<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Personal extends BaseModel
{
    use HasFactory;

    public $image_profile_collection_name = 'PROFILE';

    protected $with = ['image_profile'];

    public function image_profile()
    {
        return $this->morphOne(config('media-library.media_model'), 'model')
            ->where('collection_name', $this->image_profile_collection_name);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activities()
    {
        return $this->belongsToMany(Variable::class)->where('type', 'activity');
    }

    public function careers()
    {
        return $this->belongsToMany(Variable::class)->where('type', 'career');
    }
}
