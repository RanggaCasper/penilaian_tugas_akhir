<?php

namespace App\Models\Rubric;

use Illuminate\Database\Eloquent\Model;

class Rubric extends Model
{
    protected $table = 'rubrics';

    protected $guarded = [
        'id'
    ];

    /**
     * Get all of the rubrics criteria for the rubrics.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function criterias()
    {  
        return $this->hasMany(Criteria::class);  
    }
}
