<?php

namespace App\Models\Exam;

use App\Models\User;
use App\Models\FinalProject\Exam;
use App\Models\Evaluation\Evaluation as Evaluations;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $table = 'exam_evaluations';

    protected $guarded = [
        'id'
    ];

    /**
     * Get the student that the evaluation belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the examiner that is assigned to the evaluation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function examiner()
    {
        return $this->belongsTo(User::class, 'examiner_id');
    }

    /**
     * Get the exam that owns the evaluation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the evaluations that are related to this evaluation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function evaluations()
    {
        return $this->belongsTo(Evaluations::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'exam_evaluation_id');
    }
}
