<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostingAlert extends Model
{
    protected $fillable = [
        'hosting_id',
        'alert_level',
        'alert_day'
    ];

    public function hosting()
    {
        return $this->belongsTo(Hosting::class);
    }
}
