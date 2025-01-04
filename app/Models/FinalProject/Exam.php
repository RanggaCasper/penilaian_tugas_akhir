<?php

namespace App\Models\FinalProject;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $table = 'exams';

    protected $guarded = [
        'id'
    ];

    /**
     * Relation to the student (User) who is assigned to this final project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Relation to the primary examiner (User) who is assigned to this final project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function primary_examiner()
    {
        return $this->belongsTo(User::class, 'primary_examiner_id');
    }

    /**
     * Relation to the secondary examiner (User) who is assigned to this final project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function secondary_examiner()
    {
        return $this->belongsTo(User::class, 'secondary_examiner_id');
    }

    /**
     * Relation to the tertiary examiner (User) who is assigned to this final project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tertiary_examiner()
    {
        return $this->belongsTo(User::class, 'tertiary_examiner_id');
    }
}
