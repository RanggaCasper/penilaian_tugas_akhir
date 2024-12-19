<?php

namespace App\Models\FinalProject;

use App\Models\Generation;
use Illuminate\Database\Eloquent\Model;

class FinalProjectPeriod extends Model
{
    protected $table = 'final_project_periods';

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

    public function generation()
    {
        return $this->belongsTo(Generation::class);
    }
}
