<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email', 'team'
    ];
}
