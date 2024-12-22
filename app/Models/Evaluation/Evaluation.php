<?php

namespace App\Models\Evaluation;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $table = 'evaluations';

    protected $guarded = [
        'id'
    ];

    /**
     * Get all of the evaluation criteria for the evaluation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function evaluation_criterias()
    {  
        return $this->hasMany(EvaluationCriteria::class);  
    }
}
