<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Exam\Exam;
use Illuminate\Notifications\Notifiable;
use App\Models\FinalProject\FinalProject;
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

    public function generation()
    {
        return $this->belongsTo(Generation::class);
    }

    public function final_project()
    {
        return $this->hasOne(FinalProject::class);
    }

    public function exam()
    {
        return $this->hasOne(Exam::class, 'student_id');
    }
}
