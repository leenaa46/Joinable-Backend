<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends BaseModel
{
    use HasFactory;

    public $image_title_collection_name = 'TITLE';

    protected $with = ['image_title'];

    public function image_title()
    {
        return $this->morphOne(config('media-library.media_model'), 'model')
            ->where('collection_name', $this->image_title_collection_name);
    }

    /**
     * Scope Post For Active Company
     */
    public function scopeActiveCompany($query)
    {
        return $query->whereRelation('created_by', 'company_id', \auth()->user()->company_id);
    }

    public function post_contents()
    {
        return $this->hasMany(PostContent::class);
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * The activities that belong to the post.
     */
    public function activities()
    {
        return $this->belongsToMany(Variable::class)->where('type', 'activity');
    }

    /**
     * The feedback_statuses that belong to the post.
     */
    public function feedback_statuses()
    {
        return $this->belongsToMany(Variable::class)->where('type', 'feedback_status');
    }

    /**
     * The personals that belong to the post.
     */
    public function personals()
    {
        return $this->belongsToMany(Personal::class);
    }
}
