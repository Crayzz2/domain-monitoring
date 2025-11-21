<?php

namespace App\Livewire;

use App\Http\Controllers\UpdateExpiresDateController;
use App\Models\Domain;
use App\Models\Status;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filement\Forms\Form;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
                Domain::query()->orderBy('expiration_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->color(fn($record)=> $record->expiration_date < now('America/Sao_Paulo')->format('Y-m-d') ? 'danger' : null)
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
            ])
            ->filters([
                Tables\Filters\Filter::make('expiration_date')
                    ->form([
                        Forms\Components\DatePicker::make('expire_from')
                            ->label(__('Expire From'))
                            ->default(now('America/Sao_Paulo')->format('Y-m-d')),
                        Forms\Components\DatePicker::make('expire_until')
                            ->label(__('Expire Until'))
                            ->default(now('America/Sao_Paulo')->addMonths(3)->format('Y-m-d')),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['expire_from'] && !$data['expire_until']) {
                            return null;
                        } else if ($data['expire_from'] && ! $data['expire_until']) {
                            return __('From'). ' ' . Carbon::parse($data['expire_from'])->format('d/m/Y');
                        } else if(!$data['expire_from'] && $data['expire_until']){
                            return __('Until') . ' ' . Carbon::parse($data['expire_until'])->format('d/m/Y');
                        } else {
                            return __('From'). ' ' . Carbon::parse($data['expire_from'])->format('d/m/Y') . ' - ' . __('Until') . ' ' . Carbon::parse($data['expire_until'])->format('d/m/Y');
                        }

                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['expire_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('expiration_date', '>=', $date),
                            )
                            ->when(
                                $data['expire_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('expiration_date', '<=', $date),
                            );
                    })
            ]);
    }
}
