<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Configuration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'notification_receive_email',
        'whatsapp_message',
        'default_color',
        'domain_default_filter_days',
        'hosting_default_filter_days',
        'summary_default_interval_days',
        'company_name',
        'company_logo',
        'domain_default_message',
        'hosting_default_message',
        'send_alerts',
        'internal_alert_message_level_one',
        'internal_alert_message_level_two',
        'internal_alert_message_level_three',
        'internal_alert_message_level_four',
        'client_alert_message_level_one',
        'client_alert_message_level_two',
        'client_alert_message_level_three',
        'instance_uuid',
        'instance_status',
    ];
}
