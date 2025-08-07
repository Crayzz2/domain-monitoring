<?php

namespace App\Livewire;

use App\Models\Hosting;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class HostingStatusTableWidget extends BaseWidget
{
//    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Hospedagens';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Hosting::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('Client'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label(__('Expiration Date'))
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\SelectColumn::make('status')
                    ->label(__('Status'))
                    ->options([])
                    ->sortable()
                ,
            ]);
    }
}
