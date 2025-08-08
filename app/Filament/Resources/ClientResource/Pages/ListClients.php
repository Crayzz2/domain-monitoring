<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Http\Controllers\PrintSummaryController;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('print')
                ->icon('heroicon-o-arrow-down-tray')
                ->label(__('Summary'))
                ->url('/print'),
            Actions\CreateAction::make()
                ->modalWidth('md'),
        ];
    }
}
