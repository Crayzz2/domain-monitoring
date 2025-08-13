<?php

namespace App\Filament\Widgets;

use App\Models\Hosting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HostingExpirationWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['Super Admin', 'Painel de Controle', 'Listar', 'Editar']);
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Hospedagens a vencer em 90 Dias', function(){
                return Hosting::where('expiration_date', '<=' ,now('America/Sao_Paulo')->addMonths(3)->format('Y-m-d'))
                    ->where('expiration_date', '>' ,now('America/Sao_Paulo')->format('Y-m-d'))
                    ->count();
            }),
            Stat::make('Hospedagens Normalizados', function(){
                return Hosting::where('expiration_date', '>=' ,now('America/Sao_Paulo')->addMonths(3)->format('Y-m-d'))->count();
            }),
            Stat::make('Hospedagens Vencidas', function(){
                return Hosting::where('expiration_date', '<' ,now('America/Sao_Paulo')->format('Y-m-d'))
                    ->count();
            }),
        ];
    }
}
