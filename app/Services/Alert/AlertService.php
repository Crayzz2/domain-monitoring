<?php

namespace App\Services\Alert;

use App\Models\Configuration;
use App\Models\Hosting;
use App\Models\HostingAlert;
use App\Models\Phone;
use App\Services\Evolution\EvolutionService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AlertService
{
    public function invoke()
    {
        HostingAlert::all()->each(function (HostingAlert $alert) {
           if(Carbon::parse($alert->updated_at)->startOfDay() < Carbon::now()->startOfDay()){
               if($alert->alert_level>=4){
                   $this->fourth_step();
               } else {
                   if($alert->alert_level==1){
                       if($alert->alert_day>=37){
                           $alert->update([
                               'alert_level'=>$alert->alert_level+1,
                               'alert_day'=>1,
                           ]);
                       } else {
                           $alert->update(['alert_day'=>$alert->alert_day+1]);
                       }
                   } else {
                       if($alert->alert_day>=7){
                           $alert->update([
                               'alert_level'=>$alert->alert_level+1,
                               'alert_day'=>1,
                           ]);
                       } else {
                           $alert->update(['alert_day'=>$alert->alert_day+1]);
                       }
                   }
               }
           }
        });
        $this->first_step();
        $this->second_step();
        $this->third_step();
    }
    protected function send_alert($recipient, $message)
    {
        $evo = new EvolutionService;
        $config = Configuration::first();

        if($config->instance_status == 'open'){
            $evo->sendText($recipient, $message);
        }
    }

    protected function format_phone($phone){
        $phone = Str::remove([' ', '-', '(', ')', '+'], $phone);
        if(Str::length($phone)<13){
            $phone = '55'.$phone;
        }
        return $phone;
    }

    //4 Rotinas
    protected function first_step()
    {
        //1° 30 dias antes de vencer hospedagem envio de mensagem para Interno e Cliente e adicionar na lista de alerta
        $config = Configuration::first();
        $hostings = Hosting::where('expiration_date', Carbon::now()->startOfDay()->addDays(30)->format('Y-m-d'))->get();
        foreach ($hostings as $hosting) {
            if($hosting->client->phone){
                HostingAlert::updateOrCreate(
                    [
                        'hosting_id' => $hosting->id,
                    ],
                );

                $hosting->update(['status' => 'financial_informed']);

                $client_message = Str::replace('{nome}', $hosting->client->name, $config->client_alert_message_level_one);
                $client_message = Str::replace('{data de expiracão}', Carbon::parse($hosting->expiration_date)->format('d/m/Y'), $client_message);

                $internal_message = Str::replace('{cliente}', $hosting->client->name, $config->internal_alert_message_level_one);

                $this->send_alert($this->format_phone($hosting->client->phone), $client_message);

                Phone::where('send_alert', true)->each(function($phone) use ($internal_message){
                    $this->send_alert($this->format_phone($phone->number), $internal_message);
                });
            }
        }
    }

    protected function second_step()
    {
        //2° 7 dias após o vencimento caso status não esteja pago, mandar mensagem novamente para interno e cliente avisando
        //que será suspensa a hospedagem
        $config = Configuration::first();
        $hostings = Hosting::whereIn('id',
            HostingAlert::where('alert_level', 2)->where('alert_day', 1)->pluck('hosting_id')->toArray()
        )->get();

        foreach ($hostings as $hosting) {
            if($hosting->client->phone){
                $hosting->update(['status' => 'waiting_payment']);

                $client_message = Str::replace('{nome}', $hosting->client->name, $config->client_alert_message_level_two);
                $client_message = Str::replace('{data de expiracão}', Carbon::parse($hosting->expiration_date)->format('d/m/Y'), $client_message);

                $internal_message = Str::replace('{cliente}', $hosting->client->name, $config->internal_alert_message_level_two);

                $this->send_alert($this->format_phone($hosting->client->phone), $client_message);

                Phone::where('send_alert', true)->each(function($phone) use ($internal_message){
                    $this->send_alert($this->format_phone($phone->number), $internal_message);
                });
            }
        }
    }

    protected function third_step()
    {
        //3° 7 dias após o segundo aviso caso status não seja pago, mandar mensagem para interno e cliente avisando que a hospedagem
        //será excluida permanentemente
        $config = Configuration::first();
        $hostings = Hosting::whereIn('id',
            HostingAlert::where('alert_level', 3)->where('alert_day', 1)->pluck('hosting_id')->toArray()
        )->get();

        foreach ($hostings as $hosting) {
            if($hosting->client->phone){
                $hosting->update(['status' => 'charge_sent']);

                $client_message = Str::replace('{nome}', $hosting->client->name, $config->client_alert_message_level_three);
                $client_message = Str::replace('{data de expiracão}', Carbon::parse($hosting->expiration_date)->format('d/m/Y'), $client_message);

                $internal_message = Str::replace('{cliente}', $hosting->client->name, $config->internal_alert_message_level_three);

                $this->send_alert($this->format_phone($hosting->client->phone), $client_message);

                Phone::where('send_alert', true)->each(function($phone) use ($internal_message){
                    $this->send_alert($this->format_phone($phone->number), $internal_message);
                });
            }
        }
    }

    protected function fourth_step()
    {
        //4° 7 dias após terceiro aviso caso status não esteja pago, mandar mensagem para interno avisando que se até o fim do dia
        //não for pago será excluido no fim do dia
        $config = Configuration::first();
        $hostings = Hosting::whereIn('id',
            HostingAlert::where('alert_level', 4)->pluck('hosting_id')->toArray()
        )->get();

        foreach ($hostings as $hosting) {
            if($hosting->client->phone){
                $hosting->update(['status' => 'waiting_payment']);

                $internal_message = Str::replace('{cliente}', $hosting->client->name, $config->internal_alert_message_level_four);

                Phone::where('send_alert', true)->each(function($phone) use ($internal_message){
                    $this->send_alert($this->format_phone($phone->number), $internal_message);
                });
            }
        }
    }
}
