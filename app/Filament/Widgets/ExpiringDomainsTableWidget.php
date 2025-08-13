<?php

namespace App\Filament\Widgets;

use App\Models\Domain;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ExpiringDomainsTableWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['Super Admin', 'Painel de Controle', 'Listar', 'Editar']);
    }
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Domínios Expirando nos Próximos 90 Dias';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Domain::where('expiration_date', '<' ,now('America/Sao_Paulo')->addMonths(3)->format('Y-m-d'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->color(
                        fn($record)=>
                        Domain::where('id', $record->id)
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
                    ->label(__('Third Party Domain'))
                    ->boolean()
                    ->alignCenter()
            ]);
    }
}
