<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Exam\Exam;
use App\Models\Thesis\Thesis;
use App\Models\Exam\Assessment;
use App\Models\Proposal\Proposal;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function program_study()
    {
        return $this->belongsTo(ProgramStudy::class);
    }

    public function generation()
    {
        return $this->belongsTo(Generation::class);
    }

    public function thesis()
    {
        return $this->hasOne(Thesis::class, 'student_id');
    }

    public function proposal()
    {
        return $this->hasOne(Proposal::class, 'student_id');
    }

    public function exam()
    {
        return $this->hasOne(Exam::class, 'student_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'student_id');
    }
}
