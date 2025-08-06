<?php

namespace App\Filament\Widgets;

use App\Models\Domain;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DomainExpirationWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Domínios a vencer em 90 Dias', function(){
                return Domain::where('expiration_date', '>' ,now('America/Sao_Paulo')->addMonths(2)->format('Y-m-d'))
                    ->where('expiration_date', '<=' ,now('America/Sao_Paulo')->addMonths(3)->format('Y-m-d'))
                    ->count();
            }),
            Stat::make('Domínios a vencer em 60 Dias', function(){
                return Domain::where('expiration_date', '>' ,now('America/Sao_Paulo')->format('Y-m-d'))
                    ->where('expiration_date', '<=' ,now('America/Sao_Paulo')->addMonths(2)->format('Y-m-d'))
                    ->count();
            }),
            Stat::make('Domínios Normalizados', function(){
                return Domain::where('expiration_date', '>=' ,now('America/Sao_Paulo')->addMonths(3)->format('Y-m-d'))->count();
            }),
        ];
    }
}
