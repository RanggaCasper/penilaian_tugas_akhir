<?php

namespace App\Models\Rubrics;

use App\Models\Rubrics\Rubrics;
use Illuminate\Database\Eloquent\Model;

class SubCriteria extends Model
{
    protected $table = 'sub_criterias';

    protected $guarded = [
        'id'
    ];

    /**
     * Belongs to an Rubrics criteria.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'criteria_id');
    }
}
