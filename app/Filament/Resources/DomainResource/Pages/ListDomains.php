<?php

namespace App\Filament\Resources\DomainResource\Pages;

use App\Filament\Resources\DomainResource;
use App\Http\Controllers\UpdateExpiresDateController;
use App\Models\Configuration;
use App\Models\Domain;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ListDomains extends ListRecords
{
    protected static string $resource = DomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('update_all')
                ->label(__('Update All'))
                ->action(function(){
                    foreach(Domain::all() as $domain){
                        $update = new UpdateExpiresDateController();
                        $update->update($domain);
                    }
                }),
            Actions\CreateAction::make()
                ->label(__('Add Domain'))
                ->modalWidth('lg')
                ->modalSubmitActionLabel(__('Add'))
                ->after(function($record) {
                    $update = new UpdateExpiresDateController();
                    $response = $update->update($record);
                    if($response['type']=='error'){
                        Notification::make('error')
                            ->danger()
                            ->title($response['msg'])
                            ->send();
                    } else if($response['type']=='success'){
                        Notification::make('success')
                            ->success()
                            ->title($response['msg'])
                            ->send();
                    }
                }),
        ];
    }
}
