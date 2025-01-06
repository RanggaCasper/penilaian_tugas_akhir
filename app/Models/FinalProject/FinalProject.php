<?php

namespace App\Models\FinalProject;

use App\Models\User;
use App\Models\Period;
use Illuminate\Database\Eloquent\Model;

class FinalProject extends Model
{
    protected $table = 'final_projects';

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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
