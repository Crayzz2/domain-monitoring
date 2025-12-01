<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hosting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'expiration_date',
        'host_user',
        'host_password',
        'status',
        'value'
    ];

    public function hosting_providers()
    {
        return $this->belongsTo(HostingProviders::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
