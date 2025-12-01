<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HostingResource\Pages;
use App\Filament\Resources\HostingResource\RelationManagers;
use App\Models\Client;
use App\Models\Configuration;
use App\Models\Hosting;
use App\Models\HostingProviders;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Leandrocfe\FilamentPtbrFormFields\Money;

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
            return 'primary';
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
                    ->required()
                    ->searchable()
                    ->live()
                    ->suffixAction(
                        Forms\Components\Actions\Action::make('create')
                            ->icon('heroicon-o-plus')
                            ->form([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Enterprise'))
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('responsible_name')
                                    ->label(__('Responsible Name'))
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('phone')
                                    ->label(__('Phone'))
                                    ->mask('(99) 99999-9999')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('email')
                                    ->label(__('Email'))
                                    ->email()
                                    ->columnSpanFull(),
                            ])
                            ->modalWidth('md')
                            ->action(function ($data, $set){
                                $client = Client::create([
                                    "name" => $data['name'],
                                    "responsible_name" => $data['responsible_name'],
                                    "email" => $data['email'],
                                    "phone" => $data['phone'],
                                ]);
                                $set('client_id', $client->id);
                            })
                    ),
                Forms\Components\DatePicker::make('expiration_date')
                    ->label(__('Expiration Date'))
                    ->required(),
                Forms\Components\TextInput::make('host_user')
                    ->label(__('Username')),
                Forms\Components\TextInput::make('host_password')
                    ->label(__('Password'))
                    ->password()
                    ->revealable()
                    ->formatStateUsing(fn($state)=>$state ? Crypt::decrypt($state) : ''),
//                Forms\Components\Toggle::make('is_third_party')
//                    ->label(__('Third Party Hosting'))
//                    ->inline(false)
//                    ->live(),
//                Forms\Components\Select::make('hosting_providers_id')
//                    ->label(__('Hosting Provider'))
//                    ->options(HostingProviders::pluck('name', 'id')),
                Forms\Components\Select::make('status')
                    ->label(__('Status'))
                    ->options([
                        'financial_informed' => 'Informado ao financeiro',
                        'charge_sent' => 'Cobrança enviada',
                        'waiting_payment' => 'Aguardando Pagamento',
                        'paid' => 'Pago',
                        'dont_renew' => 'Não Renovar'
                    ]),
               Money::make('value')
                    ->label(__('Value')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('Client'))
                    ->color(function($record){
                        if(!$record->expiration_date)  return null;
                        if($record->expiration_date < now('America/Sao_Paulo')->format('Y-m-d')){
                            return 'danger';
                        }
                        return null;
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label(__('Expiration Date'))
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label(__('Value'))
                    ->money('BRL'),
//                Tables\Columns\IconColumn::make('is_third_party')
//                    ->label(__('Third Party Hosting'))
//                    ->boolean(),
//                Tables\Columns\TextColumn::make('hosting_providers.name')
//                    ->label(__('Hosting Provider')),
            ])
            ->defaultSort('expiration_date')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_credentials')
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->hidden(fn()=>!auth()->user()->hasAnyPermission(['Ver Credenciais']) && !auth()->user()->hasRole('Super Admin'))
                    ->form([
                        Forms\Components\TextInput::make('host_user')
                            ->label(__('Username'))
                            ->default(fn($record)=>$record->host_user)
                            ->readOnly(),
                        Forms\Components\TextInput::make('host_password')
                            ->label(__('Password'))
                            ->default(fn($record)=>$record->host_password ? Crypt::decrypt($record->host_password) : '')
                            ->readOnly(),
                    ])
                    ->modalWidth('md'),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('whatsapp')
                        ->label(__('Message'))
                        ->icon('heroicon-o-chat-bubble-bottom-center-text')
                        ->color('primary')
                        ->hidden(function($record){
                            $expire = false;
                            $phone = false;
                            if($record->expiration_date > now('America/Sao_Paulo')->addMonths(3)->format('Y-m-d')){
                                $expire = true;
                            }
                            if($record->client_id){
                                if(!$record->client->phone){
                                    $phone = true;
                                }
                            }
                            return !$record->client_id || !$record->expiration_date || $phone || $expire;
                        })
                        ->url(function($record){
                            $configuration = Configuration::first();
                            $message = $configuration->hosting_default_message;
                            $phone = substr($record->client->phone, 1, 2) . substr($record->client->phone, 5, 5) . substr($record->client->phone, 11);
                            if($message){
                                if(Str::contains($message, '{nome}')){
                                    $message = Str::replace('{nome}', $record->client->name, $message, false);
                                }
                                if(Str::contains($message, '{data de expiracão}')){
                                    $message = Str::replace('{data de expiracão}', Carbon::parse($record->expiration_date)->format('d/m/Y'), $message);
                                }
                                return 'https://wa.me/55' . $phone . '/?text='. urlencode($message);
                            } else {
                                return 'https://wa.me/55' . $phone;
                            }
                        })
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('update_year')
                        ->label(__('Update Year'))
                        ->color('primary')
                        ->icon('heroicon-o-arrow-path')
                        ->action(function($record){
                            if($record->expiration_date){
                                $record->expiration_date = Carbon::createFromDate($record->expiration_date)->addYears(1);
                                $record->save();
                            }
                        }),
                    Tables\Actions\Action::make('update_personalized')
                        ->label(__('Update Personalized'))
                        ->color('primary')
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
                        ->color('primary')
                        ->mutateFormDataUsing(function($data){
                            if($data['host_password']){
                                $password = Crypt::encrypt($data['host_password']);
                                $data['host_password'] = $password;
                            }
                            return $data;
                        }),
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
