<?php

namespace App\Models\Exam;

use App\Models\Exam\Score;
use App\Models\Evaluation\SubCriteria;
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
}
