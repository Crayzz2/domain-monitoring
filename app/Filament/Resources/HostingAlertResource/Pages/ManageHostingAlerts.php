<?php

namespace App\Filament\Resources\HostingAlertResource\Pages;

use App\Filament\Resources\HostingAlertResource;
use App\Services\Alert\AlertService;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHostingAlerts extends ManageRecords
{
    protected static string $resource = HostingAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
            Actions\Action::make('teste')
                ->action(function(){
                    $alert = new AlertService;
                    $alert->invoke();
                }),
        ];
    }
}
