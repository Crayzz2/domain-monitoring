<?php

namespace App\Filament\Widgets;

use App\Models\Configuration;
use App\Models\Domain;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DomainExpirationWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['Super Admin', 'Painel de Controle', 'Listar', 'Editar']);
    }
    protected function getStats(): array
    {
        $default_days = Configuration::first()?->domain_default_filter_days ?? 90;
        return [
            Stat::make(__('Domains Expiring In Next') . ' ' . $default_days . ' ' . __('Days'), function() use ($default_days){
                return Domain::where('expiration_date', '<=' ,now('America/Sao_Paulo')->addDays((integer)$default_days)->format('Y-m-d'))
                    ->where('expiration_date', '>' ,now('America/Sao_Paulo')->format('Y-m-d'))
                    ->count();
            }),
            Stat::make(__('Active Domains'), function(){
                return Domain::where('expiration_date', '>=' ,now('America/Sao_Paulo')->addMonths(3)->format('Y-m-d'))
                    ->count();
            }),
            Stat::make(__('Expired Domains'), function(){
                return Domain::where('expiration_date', '<' ,now('America/Sao_Paulo')->format('Y-m-d'))
                    ->count();
            }),
        ];
    }
}
