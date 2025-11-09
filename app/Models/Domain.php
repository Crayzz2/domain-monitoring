<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domain extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'expiration_date',
        'last_updated',
        'client_id',
        'is_third_party',
        'hosting_provider_id',
        'register_account',
        'status',
        'activity_status',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function hostingProvider()
    {
        return $this->belongsTo(HostingProviders::class);
    }

}
