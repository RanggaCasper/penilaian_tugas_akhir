<?php

namespace App\Models\Exam;

use App\Models\Exam\Score;
use App\Models\Rubric\SubCriteria;
use Illuminate\Database\Eloquent\Model;

class SubScore extends Model
{
    protected $table = 'sub_scores';

    protected $guarded = [
        'id'
    ];

    public function score()
    {
        return $this->belongsTo(Score::class);
    }

    public function sub_criteria()
    {
        return $this->belongsTo(SubCriteria::class, 'sub_criteria_id');
    }
}
