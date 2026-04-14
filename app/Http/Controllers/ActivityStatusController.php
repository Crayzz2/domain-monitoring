<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ActivityStatusController extends Controller
{
    public function update($record)
    {
        if(!$record){
            return ['type' => 'error','msg' => 'Não foi encontrado nenhum registro'];
        }

        try{
            $response = Http::head("https://".$record->name);
            sleep(5);
        } catch (\Exception $e){
            $record->activity_status = false;
            $record->save();
            return ['type' => 'error','msg' => 'Site está inativo ou com erro'];
        }

        if(!$response->successful()){
            $record->activity_status = false;
            $record->save();
            return ['type' => 'error','msg' => 'Site está inativo ou com erro'];
        }

        $record->activity_status = true;
        $record->save();

        return ['type' => 'success','msg' => 'Site ativo e funcionando'];

    }
}
