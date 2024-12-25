<?php

namespace App\Models\Evaluation;

use App\Models\Evaluation\Evaluation;
use Illuminate\Database\Eloquent\Model;

class SubCriteria extends Model
{
    protected $table = 'sub_evaluation_criterias';

    protected $guarded = [
        'id'
    ];

    /**
     * Belongs to an evaluation criteria.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function evaluation_criteria()
    {
        return $this->belongsTo(Criteria::class);
    }
}
