<?php

namespace App\Models\Rubrics;

use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    protected $table = 'criterias';

    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'has_sub' => 'boolean',
        ];
    }

    /**
     * Belongs to an rubric.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rubric()  
    {  
        return $this->belongsTo(Rubric::class);  
    }  

    /**
     * Has many sub evaluation criteria.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sub_criterias()
    {
        return $this->hasMany(SubCriteria::class, 'sub_criteria_id');
    }
}
