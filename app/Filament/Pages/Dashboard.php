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
                ->url('/print', true)
                ->hidden(fn()=>!auth()->user()->hasAnyRole(['Super Admin', 'Painel de Controle', "Listar", 'Editar'])),
            \Filament\Actions\Action::make('expired')
                ->icon('heroicon-o-arrow-down-tray')
                ->label(__('Expired'))
                ->modalHeading(__('Expired'))
                ->modalContent(fn()=>view('expired-report-modal'))
                ->modalCancelAction(false)
                ->modalSubmitAction(false)
                ->modalWidth('lg')
                ->hidden(fn()=>!auth()->user()->hasAnyRole(['Super Admin', 'Painel de Controle', "Listar", 'Editar'])),
        ];
    }
}
