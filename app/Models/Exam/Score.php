<?php

namespace App\Models\Exam;

use App\Models\Rubric\Criteria;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $table = 'scores';

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'has_sub' => 'boolean'
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'criteria_id');
    }

    public function sub_scores()
    {
        return $this->hasMany(SubScore::class, 'score_id');
    }
}
