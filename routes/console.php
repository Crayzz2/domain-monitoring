<?php

use App\Http\Controllers\UpdateExpiresDateController;
use App\Http\Controllers\ActivityStatusController;
use App\Models\User;
use App\Models\Domain;
use App\Models\Configuration;
use App\Models\Hosting;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Str;
use Resend\Laravel\Facades\Resend;
use Carbon\Carbon;
use App\Services\Alert\AlertService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function(){
    $configuration = Configuration::first();
    $days = $configuration->summary_default_interval_days ?? 90;
    $domains = Domain::where('expiration_date', '<', now('America/Sao_Paulo')->addDays((integer)$days))
        ->where('expiration_date', '>', now('America/Sao_Paulo'))->get();
    $hostings = Hosting::where('expiration_date', '<', now('America/Sao_Paulo')->addDays((integer)$days))
        ->where('expiration_date', '>', now('America/Sao_Paulo'))->get();

    $html = '<h1>Resumo dos próximos ' . $days . ' dias</h1><br>
                                 <h2>Domínios</h2>
                                 <table border=1>
                                 <thead>
                                    <tr>
                                        <td>Domínio</td>
                                        <td>Data de Expiração</td>
                                        <td>Cliente</td>
                                    </tr>
                                 </thead>
                                 <tbody>';

    foreach($domains as $domain){
        $client_name = $domain->client_id ? $domain->client->name : "";
        $html .= '<tr>
                                        <td>'. $domain->name .'</td>
                                        <td>'. Carbon::createFromDate($domain->expiration_date)->format('d/m/Y') .'</td>
                                        <td>'. $client_name .' </td>
                                      </tr>';
    }
    $html .= '</tbody></table><br><br>';


    $html .= '<h2>Hospedagens</h2>
                                 <table border=1>
                                 <thead>
                                    <tr>
                                        <td>Cliente</td>
                                        <td>Data de Expiração</td>
                                    </tr>
                                 </thead>
                                 <tbody>';

    foreach($hostings as $hosting){
        $client_name = $hosting->client_id ? $hosting->client->name : "";
        $html .= '<tr>
                                        <td>'. $client_name .' </td>
                                        <td>'. Carbon::createFromDate($hosting->expiration_date)->format('d/m/Y') .'</td>
                                      </tr>';
    }
    $html .= '</tbody></table>';

    Resend::emails()->send([
        'from' => env('APP_NAME').' <'.env('MAIL_FROM_ADDRESS').'>',
        'to' => $configuration?->notification_receive_email,
        'subject' => 'Relatório dos próximos '.$days.' dias',
        'html' => Str::squish($html)
    ]);
})->days(15);

Schedule::call(function(){
    $users = User::role(['Super Admin'])->get();

    Domain::get()->each(function($domain) use ($users){
        try{
            $activity_controller = new ActivityStatusController();

            $old_status = $domain->activity_status;

            $update = $activity_controller->update($domain);

            if($update['type'] == "error"){
                if($old_status != $domain->activity_status){
                    $title = "Novo site inativo ou fora do ar, favor verificar!";
                } else {
                    $title = "Site inativo ou fora do ar";
                }
                foreach($users as $user){
                    $user->notify(
                        Notification::make('error')
                            ->danger()
                            ->title($title)
                            ->body($domain->name)
                            ->toDatabase()
                    );
                }
            }
        } catch (Exception $e){}
    });
})->hourly();

Schedule::call(function(){
    if(Configuration::first()->send_alerts){
        $alert = new AlertService;
        $alert->invoke();
    }
})->daily();
