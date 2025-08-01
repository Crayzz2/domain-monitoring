<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UpdateExpiresDateController extends Controller
{
    public function update($record)
    {
        if(!$record){
            return 'Missing Record';
        }
        $response = Http::get('https://rdap.org/domain/'.$record->name);
        $expiresDate = null;
        if(!$response->json()){
            return ['type' => 'error','msg' => 'Verifique se o domínio é existente!'];
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
