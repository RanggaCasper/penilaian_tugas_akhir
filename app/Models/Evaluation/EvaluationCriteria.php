<?php

namespace App\Models\Evaluation;

use App\Models\Evaluation\Evaluation;
use Illuminate\Database\Eloquent\Model;

class EvaluationCriteria extends Model
{
    protected $table = 'evaluation_criterias';

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
     * Belongs to an evaluation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function evaluation()  
    {  
        return $this->belongsTo(Evaluation::class);  
    }  

    /**
     * Has many sub evaluation criteria.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sub_evaluation_criterias()
    {
        return $this->hasMany(SubEvaluationCriteria::class);
    }
}
