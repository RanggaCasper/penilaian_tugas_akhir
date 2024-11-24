<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeProposal extends Model
{
    protected $table = 'periode_proposal';

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
