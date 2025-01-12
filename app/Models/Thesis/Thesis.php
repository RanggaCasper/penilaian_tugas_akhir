<?php

namespace App\Models\Thesis;

use App\Models\User;
use App\Models\Period;
use Illuminate\Database\Eloquent\Model;

class Thesis extends Model
{
    protected $table = 'thesis';

    protected $guarded = [
        'id'
    ];

    /**
     * Relation with Period model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id');
    }

    /**
     * Relation with User model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
