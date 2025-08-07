<?php

namespace App\Livewire;

use App\Models\Domain;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class DomainStatusTableWidget extends BaseWidget
{

//    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Domínios';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Domain::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label(__('Expiration Date'))
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\SelectColumn::make('status')
                    ->label(__('Status'))
                    ->options([])
                    ->sortable(),
            ]);
    }
}
