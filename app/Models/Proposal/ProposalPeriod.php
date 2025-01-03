<?php

namespace App\Models\Proposal;

use Illuminate\Database\Eloquent\Model;

class ProposalPeriod extends Model
{
    protected $table = 'proposal_periods';

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
