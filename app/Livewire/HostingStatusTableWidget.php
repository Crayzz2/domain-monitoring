<?php

namespace App\Livewire;

use App\Models\Hosting;
use App\Models\Status;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
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
                Hosting::where('expiration_date', '<' ,now('America/Sao_Paulo')->addMonths(2)->format('Y-m-d'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('Client'))
                    ->color(
                        fn($record)=>
                            Hosting::where('id', $record->id)
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
                        if (Str::contains($record->status->name, 'Não Renovar')){
                            $record->delete();
                        }
                    })
                    ->sortable()
                ,
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
