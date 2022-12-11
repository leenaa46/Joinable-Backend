<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Personal extends BaseModel
{
    use HasFactory;

    public $image_profile_collection_name = 'PROFILE';

    protected $appends = [
        "work_year",
        "same_careers",
        "same_activities"
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

    public function getSameCareersAttribute()
    {
        $same = [];
        if (!\auth()->user()) return $same;

        if (!\auth()->user()->hasRole('employee')) return $same;

        $friendCareers = $this->careers;
        if (\count($friendCareers) <= 0) return $same;

        $ownCareers = \auth()->user()->personal->careers;
        if (\count($ownCareers) <= 0) return $same;

        foreach ($friendCareers as $friendCareer) {
            foreach ($ownCareers as $ownCareer) {
                if ($friendCareer->id == $ownCareer->id) {
                    $same[] = $friendCareer;
                    continue;
                }
            }
        }

        return $same;
    }

    public function getSameActivitiesAttribute()
    {
        $same = [];
        if (!\auth()->user()) return $same;

        if (!\auth()->user()->hasRole('employee')) return $same;

        $friendActivities = $this->activities;
        if (\count($friendActivities) <= 0) return $same;

        $ownActivities = \auth()->user()->personal->activities;
        if (\count($ownActivities) <= 0) return $same;

        foreach ($friendActivities as $friendActivity) {
            foreach ($ownActivities as $ownActivity) {
                if ($friendActivity->id == $ownActivity->id) {
                    $same[] = $friendActivity;
                    continue;
                }
            }
        }

        return $same;
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
