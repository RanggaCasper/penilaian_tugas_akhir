<?php

namespace App\Models\Mentor;

use App\Models\Proposal\Proposal;
use App\Models\User;
use App\Models\Rubric\Criteria;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $table = 'mentor_scores';

    protected $guarded = [
        'id'
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
}
