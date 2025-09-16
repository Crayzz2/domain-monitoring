<?php

namespace App\Filament\Resources\HostingResource\Pages;

use App\Filament\Resources\HostingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Crypt;

class ListHostings extends ListRecords
{
    protected static string $resource = HostingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('md')
                ->mutateFormDataUsing(function($data){
                    if($data['host_password']){
                        $password = Crypt::encrypt($data['host_password']);
                        $data['host_password'] = $password;
                    }
                    return $data;
                }),
        ];
    }
}
