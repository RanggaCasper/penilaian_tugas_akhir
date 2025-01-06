<?php

namespace App\Models\Proposal;

use App\Models\User;
use App\Models\Period;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    protected $table = 'proposals';

    protected $guarded = [
        'id'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    } 

    public function primary_mentor()
    {
        return $this->belongsTo(User::class, 'primary_mentor_id');
    }

    public function secondary_mentor()
    {
        return $this->belongsTo(User::class, 'secondary_mentor_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id');
    }
}
