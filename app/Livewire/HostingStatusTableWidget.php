<?php

namespace App\Livewire;

use App\Models\Hosting;
use App\Models\Status;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use Filament\Forms\Form;


class HostingStatusTableWidget extends BaseWidget implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

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
                    ->sortable()
                ,
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
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('update_year')
                        ->label(__('Update Year'))
                        ->icon('heroicon-o-arrow-path')
                        ->action(function($record){
                            if($record->expiration_date){
                                $record->expiration_date = Carbon::createFromDate($record->expiration_date)->addYears(1);
                                $record->save();
                            }
                        }),
                    Tables\Actions\Action::make('update_personalized')
                        ->label(__('Update Personalized'))
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Group::make([
                                Forms\Components\TextInput::make('time')
                                    ->label(__('Tempo'))
                                    ->numeric()
                                    ->required(),
                                Forms\Components\Select::make('type')
                                    ->label(__('In'))
                                    ->options([
                                        'months' => __('Months'),
                                        'years' => __('Years')
                                    ])
                                    ->required()
                                    ->default('months'),
                            ])->columns(2),
                        ])
                        ->modalWidth('md')
                        ->action(function($record, $data){
                            if($record->expiration_date){
                                if($data['type'] == 'months'){
                                    $record->expiration_date = Carbon::createFromDate($record->expiration_date)->addMonths((integer)$data['time']);
                                } else if($data['type'] == 'years'){
                                    $record->expiration_date = Carbon::createFromDate($record->expiration_date)->addYears((integer)$data['time']);
                                }
                                $record->save();
                            }
                        }),
                ]),
            ]);
    }
}
