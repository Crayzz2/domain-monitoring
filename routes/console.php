<?php

use App\Http\Controllers\UpdateExpiresDateController;
use App\Models\Domain;
use App\Models\Configuration;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Str;
use Resend\Laravel\Facades\Resend;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function(){
    $configuration = Configuration::first();
    if(!$configuration->notification_receive_email) {
        return;
    }
    foreach(Domain::all() as $domain){
        if(!$domain->expiration_date){
            return;
        }

        $diff = Carbon::parse(Carbon::now('America/Sao_Paulo')->format('Y-m-d'))->diffInDays(Carbon::parse($domain->expiration_date));

        if($diff == 20){
            Resend::emails()->send([
                'from' => env('APP_NAME').' <'.env('MAIL_FROM_ADDRESS').'>',
                'to' => $configuration->notification_receive_email,
                'subject' => 'Domínio Prestes a Expirar',
                'html' => '<h3>O Domínio ' . $domain->name . ' vai expirar em 20 dias.</h3><p>Não se esqueça de entrar em contato com o dono do domínio</p>'
            ]);
        }

        if($diff <= 0){
            $update = new UpdateExpiresDateController();
            $update->update($domain);
        }
    }

})->daily();
