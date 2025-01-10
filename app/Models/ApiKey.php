<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $table = 'api_keys';

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'ips' => 'array'
    ];

}
