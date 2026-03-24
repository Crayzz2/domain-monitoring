<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PhoneResource\Pages;
use App\Filament\Resources\PhoneResource\RelationManagers;
use App\Models\Phone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PhoneResource extends Resource
{
    protected static ?string $model = Phone::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    public static function getNavigationGroup(): ?string
    {
        return __('Alerts');
    }

    public static function getModelLabel(): string
    {
        return __('Phone');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('description')
                    ->label(__('Description'))
                    ->required(),
                Forms\Components\TextInput::make('number')
                    ->label(__('Number'))
                    ->required()
                    ->mask('(99) 99999-9999')
                    ->minLength(15)
                    ->validationMessages([
                        'min' => ':Attribute está incompleto.',
                        'max' => ':Attribute excede o valor máximo.',
                    ]),
                Forms\Components\Toggle::make('send_alert')
                    ->label(__('Send Alert'))
                    ->default(true)
                    ->required(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->label(__('Number'))
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('send_alert')
                    ->label(__('Send Alert')),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePhones::route('/'),
        ];
    }
}
