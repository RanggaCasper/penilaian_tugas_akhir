<?php

namespace App\Models\Exam;

use App\Models\Exam\Score;
use App\Models\Evaluation\SubCriteria;
use Illuminate\Database\Eloquent\Model;

class SubScore extends Model
{
    protected $table = 'sub_evaluation_scores';

    protected $guarded = [
        'id'
    ];

    /**
     * Get the criteria that owns the Score
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sub_criteria()
    {
        return $this->belongsTo(SubCriteria::class, 'sub_evaluation_criteria_id');
    }

    /**
     * Get the evaluation that owns the Score
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function score()
    {
        return $this->belongsTo(Score::class);
    }

}
