<?php

namespace App\Models;

use App\Models\Generation;
use App\Models\Proposal\Proposal;
use App\Models\Thesis\Thesis;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $table = 'periods';

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

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'period_id');
    }

    public function thesis()
    {
        return $this->hasMany(Thesis::class, 'period_id');
    }
}
