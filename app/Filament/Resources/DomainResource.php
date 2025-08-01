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
        return __('General');
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
        return static::getModel()::count();
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
                Forms\Components\TextInput::make('host_user')
                    ->label(__('Username'))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('host_password')
                    ->label(__('Password'))
                    ->password()
                    ->columnSpanFull()
                    ->revealable(true),
                Forms\Components\Toggle::make('is_third_party')
                    ->label(__('Third Party Hosting')),
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
                Tables\Columns\ColumnGroup::make(__('Hosting'), [
                    Tables\Columns\ToggleColumn::make('is_third_party')
                        ->label(__('Third Party Hosting'))
                        ->alignCenter(),
                    Tables\Columns\TextColumn::make('host_user')
                        ->label(__('Username')),
                    Tables\Columns\TextColumn::make('host_password')
                        ->label(__('Password'))
                        ->formatStateUsing(fn($state)=> str_repeat('*', Str::length($state))),
                ]),
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
