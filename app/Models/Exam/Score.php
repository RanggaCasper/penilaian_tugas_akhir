<?php

namespace App\Models\Exam;

use App\Models\Exam\Evaluation;
use App\Models\Evaluation\Criteria;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $table = 'evaluation_scores';

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'has_sub' => 'boolean'
    ];

    /**
     * Get the criteria that owns the Score
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'evaluation_criteria_id');
    }

    /**
     * Get the evaluation that owns the Score
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exam()
    {
        return $this->belongsTo(Evaluation::class, 'exam_evaluation_id');
    }


    public function sub_scores()
    {
        return $this->hasMany(SubScore::class, 'score_id');
    }
}
