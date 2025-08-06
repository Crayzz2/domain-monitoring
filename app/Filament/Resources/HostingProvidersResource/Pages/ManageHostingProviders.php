<?php

namespace App\Filament\Resources\HostingProvidersResource\Pages;

use App\Filament\Resources\HostingProvidersResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHostingProviders extends ManageRecords
{
    protected static string $resource = HostingProvidersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('md'),
        ];
    }
}
