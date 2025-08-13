<?php

namespace App\Filament\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('print')
                ->icon('heroicon-o-arrow-down-tray')
                ->label(__('Summary'))
                ->url('/print')
                ->hidden(fn()=>!auth()->user()->hasAnyRole(['Super Admin', 'Painel de Controle', "Listar", 'Editar'])),
        ];
    }
}
