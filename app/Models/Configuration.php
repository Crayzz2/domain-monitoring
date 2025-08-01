<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Configuration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'notification_receive_email',
    ];
}
