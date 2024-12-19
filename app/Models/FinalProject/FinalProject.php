<?php

namespace App\Models\FinalProject;

use Illuminate\Database\Eloquent\Model;

class FinalProject extends Model
{
    protected $table = 'final_projects';

    protected $guarded = [
        'id'
    ];

    /**
     * Relation with FinalProjectPeriod model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function period()
    {
        return $this->belongsTo(FinalProjectPeriod::class, 'final_project_period_id');
    }
}
