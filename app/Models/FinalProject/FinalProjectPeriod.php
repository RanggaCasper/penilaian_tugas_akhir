<?php

namespace App\Models\FinalProject;

use Illuminate\Database\Eloquent\Model;

class FinalProjectPeriod extends Model
{
    protected $table = 'final_project_period';

    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
