<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramStudy extends Model
{
    protected $table = 'program_studies';

    protected $guarded = [
        'id'
    ];

    /**
     * The users that belong to the Program Study
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'program_study_id');
    }
}
