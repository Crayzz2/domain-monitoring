<?php

namespace App\Livewire;

use App\Http\Controllers\UpdateExpiresDateController;
use App\Models\Domain;
use App\Models\Status;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Str;
use function PHPUnit\Framework\stringContains;

class DomainStatusTableWidget extends BaseWidget
{

//    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Domínios';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Domain::where('expiration_date', '<' ,now('America/Sao_Paulo')->addMonths(2)->format('Y-m-d'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->color(
                        fn($record)=>
                            Domain::where('id', $record->id)
                                ->pluck('expiration_date')
                                ->first() <
                            now('America/Sao_Paulo')->format('Y-m-d') ? 'danger' : null
                    )
                    ->searchable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label(__('Expiration Date'))
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\SelectColumn::make('status_id')
                    ->label(__('Status'))
                    ->options(Status::pluck('name', 'id'))
                    ->afterStateUpdated(function($record){
                        if(Str::contains($record->status->name, 'Pago')){
                            $update = new UpdateExpiresDateController();
                            $update->update($record);
                        } else if (Str::contains($record->status->name, 'Não Renovar')){
                            $record->delete();
                        }
                    })
                    ->sortable(),
            ]);
    }
}