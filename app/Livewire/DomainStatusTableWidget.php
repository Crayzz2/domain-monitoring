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
                Tables\Columns\SelectColumn::make('status')
                    ->label(__('Status'))
                    ->options([
                        'financial_informed' => 'Informado ao financeiro',
                        'charge_sent' => 'Cobrança enviada',
                        'waiting_payment' => 'Aguardando Pagamento',
                        'paid' => 'Pago',
                        'dont_renew' => 'Não Renovar'
                    ])
                    ->afterStateUpdated(function($record){
                        if($record->status == "paid"){
                            $update = new UpdateExpiresDateController();
                            $update->update($record);
                        } else if ($record->status == 'dont_renew'){
                            $record->delete();
                        }
                    })
                    ->sortable(),
            ]);
    }
}
