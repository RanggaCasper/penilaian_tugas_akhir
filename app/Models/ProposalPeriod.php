<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalPeriod extends Model
{
    protected $table = 'proposal_period';

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
