<?php

namespace App\Filament\Widgets;

use App\Models\Hosting;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ExpiringHostingsTableWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['Super Admin', 'Painel de Controle', 'Listar', 'Editar']);
    }
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Hospedagens Expirando nos Próximos 90 Dias';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Hosting::where('expiration_date', '<=' ,now('America/Sao_Paulo')->addMonths(3)->format('Y-m-d'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('Name'))
                    ->color(
                        fn($record)=>
                        Hosting::where('id', $record->id)
                            ->pluck('expiration_date')
                            ->first() <
                        now('America/Sao_Paulo')->format('Y-m-d') ? 'danger' : null
                    )
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label(__('Expiration Date'))
                    ->date('d/m/Y')
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_third_party')
                    ->label(__('Third Party Hosting'))
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('hosting_providers.name')
                    ->label(__('Hosting Provider')),
            ]);
    }
}
