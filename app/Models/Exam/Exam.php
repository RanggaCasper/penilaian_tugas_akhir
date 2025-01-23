<?php

namespace App\Models\Exam;

use App\Models\User;
use App\Models\Rubric\Rubric;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $table = 'exams';

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'is_editable' => 'boolean'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function primary_examiner()
    {
        return $this->belongsTo(User::class, 'primary_examiner_id');
    }

    public function secondary_examiner()
    {
        return $this->belongsTo(User::class, 'secondary_examiner_id');
    }

    public function tertiary_examiner()
    {
        return $this->belongsTo(User::class, 'tertiary_examiner_id');
    }

    public function rubric()
    {
        return $this->belongsTo(Rubric::class, 'rubric_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'exam_id');
    }
}
