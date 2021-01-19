<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group_read extends Model
{
    protected $fillable = [
        'group_id', 'user_id', 'msg_id', 'read'
    ];
}
