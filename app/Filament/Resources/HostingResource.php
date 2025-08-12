<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HostingResource\Pages;
use App\Filament\Resources\HostingResource\RelationManagers;
use App\Models\Client;
use App\Models\Hosting;
use App\Models\HostingProviders;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HostingResource extends Resource
{
    protected static ?string $model = Hosting::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    public static function getNavigationGroup(): ?string
    {
        return __('Hosting/Domain');
    }

    public static function getModelLabel(): string
    {
        return __('Hosting');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Hostings');
    }
    public static function getNavigationBadge(): ?string
    {
        $expired = Hosting::where('expiration_date', '<' ,now('America/Sao_Paulo')->format('Y-m-d'))->count();
        $toExpire = Hosting::where('expiration_date', '>' ,now('America/Sao_Paulo')->format('Y-m-d'))
            ->where('expiration_date', '<=', now('America/Sao_Paulo')->addMonths(1)->format('Y-m-d'))->count();
        if($expired > 0){
            return 'Vencidos ('.$expired.')';
        } else if($toExpire > 0){
            return 'A Vencer ('.$toExpire.')';
        }
        return null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $expired = Hosting::where('expiration_date', '<' ,now('America/Sao_Paulo')->format('Y-m-d'))->count();
        $toExpire = Hosting::where('expiration_date', '>' ,now('America/Sao_Paulo')->format('Y-m-d'))
            ->where('expiration_date', '<=', now('America/Sao_Paulo')->addMonths(1)->format('Y-m-d'))->count();
        if($expired > 0){
            return 'danger';
        } else if($toExpire > 0){
            return 'warning';
        } else {
            return 'success';
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->label(__('Client'))
                    ->options(Client::pluck('name', 'id'))
                    ->required(),
                Forms\Components\DatePicker::make('expiration_date')
                    ->label(__('Expiration Date')),
                Forms\Components\TextInput::make('host_user')
                    ->label(__('Username')),
                Forms\Components\TextInput::make('host_password')
                    ->label(__('Password'))
                    ->password()
                    ->revealable(),
                Forms\Components\Toggle::make('is_third_party')
                    ->label(__('Third Party Hosting'))
                    ->inline(false)
                    ->live(),
                Forms\Components\Select::make('hosting_providers_id')
                    ->label(__('Hosting Provider'))
                    ->options(HostingProviders::pluck('name', 'id')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('Client'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label(__('Expiration Date'))
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_third_party')
                    ->label(__('Third Party Hosting'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('hosting_providers.name')
                    ->label(__('Hosting Provider')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_credentials')
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->form([
                        Forms\Components\TextInput::make('host_user')
                            ->label(__('Username'))
                            ->default(fn($record)=>$record->host_user)
                            ->readOnly(),
                        Forms\Components\TextInput::make('host_password')
                            ->label(__('Password'))
                            ->default(fn($record)=>$record->host_password)
                            ->readOnly(),
                    ])
                    ->modalWidth('md'),
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
                    Tables\Actions\EditAction::make()
                        ->modalWidth('md')
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make()
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHostings::route('/'),
//            'create' => Pages\CreateHosting::route('/create'),
//            'edit' => Pages\EditHosting::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
