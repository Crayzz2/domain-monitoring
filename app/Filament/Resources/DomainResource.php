<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DomainResource\Pages;
use App\Filament\Resources\DomainResource\RelationManagers;
use App\Http\Controllers\UpdateExpiresDateController;
use App\Models\Client;
use App\Models\Configuration;
use App\Models\Domain;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\View\Components\Modal;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Resend\Laravel\Facades\Resend;

class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;

    protected static ?int $navigationSort = 0;
    protected static ?string $navigationIcon = 'heroicon-o-at-symbol';

    public static function getNavigationGroup(): string
    {
        return __('Hosting/Domain');
    }
    public static function getNavigationLabel(): string
    {
        return __('Domains');
    }

    public static function getModelLabel(): string
    {
        return __('Domain');
    }
    public static function getNavigationBadge(): ?string
    {
        $expired = Domain::where('expiration_date', '<' ,now('America/Sao_Paulo')->format('Y-m-d'))->count();
        $toExpire = Domain::where('expiration_date', '>' ,now('America/Sao_Paulo')->format('Y-m-d'))
            ->where('expiration_date', '<=', now('America/Sao_Paulo')->addMonths(2)->format('Y-m-d'))->count();
        if($expired > 0){
            return 'Vencidos ('.$expired.')';
        } else if($toExpire > 0){
            return 'A Vencer ('.$toExpire.')';
        }
        return null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $expired = Domain::where('expiration_date', '<' ,now('America/Sao_Paulo')->format('Y-m-d'))->count();
        $toExpire = Domain::where('expiration_date', '>' ,now('America/Sao_Paulo')->format('Y-m-d'))
            ->where('expiration_date', '<=', now('America/Sao_Paulo')->addMonths(2)->format('Y-m-d'))->count();
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
                Forms\Components\TextInput::make('name')
                    ->label(__('Domain'))
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('client_id')
                    ->label(__('Enterprise'))
                    ->options(Client::pluck('name', 'id'))
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_third_party')
                    ->label(__('Third Party Hosting'))
                    ->inline(false),
                Forms\Components\TextInput::make('register_account')
                    ->label(__('Account')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Domain'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('Enterprise'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label(__('Expiration Date'))
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_updated')
                    ->label(__('Last Updated'))
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_third_party')
                    ->label(__('Third Party Domain'))
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('register_account')
                    ->label(__('Account')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->modalWidth('md')
                        ->color('warning'),
                    Tables\Actions\Action::make('update')
                        ->label(__('Update'))
                        ->action(function($record){
                            $update = new UpdateExpiresDateController();
                            $response = $update->update($record);
                            if($response['type']=='error'){
                                Notification::make('error')
                                    ->danger()
                                    ->title($response['msg'])
                                    ->send();
                            } else if($response['type']=='success'){
                                Notification::make('success')
                                    ->success()
                                    ->title($response['msg'])
                                    ->send();
                            }
                        })
                        ->color('warning')
                        ->icon('heroicon-o-arrow-path'),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make()
                ]),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListDomains::route('/'),
//            'create' => Pages\CreateDomain::route('/create'),
//            'edit' => Pages\EditDomain::route('/{record}/edit'),
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
