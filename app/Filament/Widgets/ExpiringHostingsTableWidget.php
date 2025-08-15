<?php

namespace App\Filament\Widgets;

use App\Models\Configuration;
use App\Models\Hosting;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;

class ExpiringHostingsTableWidget extends BaseWidget
{
    public $default_days;
    public function mount(){
        $this->default_days = Configuration::first()?->hosting_default_filter_days ?? 90;
    }
    public function getTableHeading(): string|Htmlable|null
    {
        return __('Hostings Expiring In Next') . ' ' . $this->default_days . ' ' . __('Days');
    }
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['Super Admin', 'Painel de Controle', 'Listar', 'Editar']);
    }
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Hosting::where('expiration_date', '<=' ,now('America/Sao_Paulo')->addDays((integer)$this->default_days)->format('Y-m-d'))
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
