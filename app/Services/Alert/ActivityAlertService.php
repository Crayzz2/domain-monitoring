<?php

namespace App\Services\Alert;

use App\Http\Controllers\ActivityStatusController;
use App\Models\Domain;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ActivityAlertService
{
    public function invoke()
    {
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
            } catch (\Exception $e){
                Log::error($e->getMessage());
            }
        });
    }
}
