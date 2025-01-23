<?php

namespace App\Models\Exam;

use App\Models\Rubric\Rubric;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $table = 'assessments';

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'is_exam' => 'boolean'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function examiner()
    {
        return $this->belongsTo(User::class, 'examiner_id');
    }

    public function rubric()
    {
        return $this->belongsTo(Rubric::class, 'rubric_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'assessment_id');
    }

    public function revisions()
    {
        return $this->hasMany(Revision::class, 'assessment_id');
    }
}
