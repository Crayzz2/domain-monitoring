<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HostingAlertResource\Pages;
use App\Filament\Resources\HostingAlertResource\RelationManagers;
use App\Models\HostingAlert;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HostingAlertResource extends Resource
{
    protected static ?string $model = HostingAlert::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    public static function getNavigationGroup(): string
    {
        return __('Alerts');
    }

    public static function getModelLabel(): string
    {
        return __('Hosting Alert');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Hosting Alerts');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hosting.client.name')
                    ->label(__('Hosting'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('hosting.expiration_date')
                    ->label(__('Expiration Date'))
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('alert_level')
                    ->label(__('Alert Level'))
                    ->formatStateUsing(function($state){
                        switch ($state) {
                            case 1:
                                return '30 Dias para vencer';
                            case 2:
                                return '7 Dias após vencimento';
                            case 3:
                                return '7 Dias para excluir permanentemente';
                            case 4:
                                return 'Dia da exclusão permanente';
                        }
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('alert_day')
                    ->label(__('Alert Day'))
                    ->sortable(),
            ])
            ->filters([])
            ->actions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageHostingAlerts::route('/'),
        ];
    }
}
