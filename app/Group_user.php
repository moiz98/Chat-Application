<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group_user extends Model
{
    protected $fillable = [
        'group_id', 'user_id'
    ];
}
