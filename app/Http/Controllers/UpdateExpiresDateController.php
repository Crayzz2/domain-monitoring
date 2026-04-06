<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateExpiresDateController extends Controller
{
    public function update($record)
    {
        if(!$record){
            return ['type' => 'error','msg' => 'Não foi encontrado nenhum registro'];
        }

        try{
            $response = Http::get('https://rdap.org/domain/'.$record->name);
            sleep(5);
        } catch(\Exception $e){
            Log::error($e->getMessage());
            return ['type' => 'error','msg' => "Não foi possível verificar domínio, tente novamente!"];
        }

        $expiresDate = null;
        if(!$response->json()){
            return ['type' => 'error','msg' => 'Não foi possível verificar domínio, tente novamente!'];
        }

        if(!$record->register_account){
            try{
                $record->register_account = $response->json()['entities'][1]['handle'];
            } catch (\Exception $e){
                $record->register_account = "-";
            }
            $record->save();
        }

        if(!isset($response->json()['events'])){
            return ['type' => 'error','msg' => 'Este domínio não possui data de expiração!'];
        }

        foreach($response->json()['events'] as $event){
            if($event['eventAction'] == 'expiration'){
                $expiresDate = $event['eventDate'];
            }
        }

        if(!$expiresDate){
            return ['type' => 'error','msg' => 'Este domínio não possui data de expiração!'];
        }

        $record->expiration_date = Carbon::createFromTimeString($expiresDate, 'America/Sao_Paulo')->format('Y-m-d');
        $record->last_updated = now('America/Sao_Paulo')->format('Y-m-d');
        $record->save();

        return ['type' => 'success', 'msg' => 'Expiração atualizada com sucesso!'];
    }
}
