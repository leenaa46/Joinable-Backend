<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Variable extends BaseModel
{
    use HasFactory;

    public $image_logo_collection_name = 'LOGO';

    protected $with = ['image_logo'];

    public function image_logo()
    {
        return $this->morphOne(config('media-library.media_model'), 'model')
            ->where('collection_name', $this->image_logo_collection_name);
    }

    /**
     * Scope Active Company
     */
    public function scopeActiveCompany($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('company_id')
                ->orWhere('company_id', \auth()->user()->company_id);
        });
    }

    public function personals()
    {
        return $this->belongsToMany(Personal::class);
    }

    public function personals_active()
    {
        return $this->personals()->whereRelation('user', 'company_id', \auth()->user()->company_id);
    }
}
