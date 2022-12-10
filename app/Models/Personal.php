<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Personal extends BaseModel
{
    use HasFactory;

    public $image_profile_collection_name = 'PROFILE';

    protected $appends = [
        "work_year"
    ];

    protected $with = ['image_profile'];

    public function image_profile()
    {
        return $this->morphOne(config('media-library.media_model'), 'model')
            ->where('collection_name', $this->image_profile_collection_name);
    }

    public function getWorkYearAttribute()
    {
        return $this->joined_at ? Carbon::parse($this->joined_at)->diffInYears() : null;
    }

    /**
     * Scope Personal By Active Company
     */
    public function scopeActiveCompany($query)
    {
        return $query->whereRelation('user', 'company_id', \auth()->user()->company_id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function variables()
    {
        return $this->belongsToMany(Variable::class);
    }

    public function activities()
    {
        return $this->belongsToMany(Variable::class)->where('type', 'activity');
    }

    public function careers()
    {
        return $this->belongsToMany(Variable::class)->where('type', 'career');
    }

    /**
     * The posts that belong to the personal.
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
