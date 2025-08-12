<?php

namespace App\Filament\Pages;

use App\Livewire\DomainStatusTableWidget;
use App\Livewire\HostingStatusTableWidget;
use Filament\Pages\Page;

class StatusPage extends Page
{

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.status-page';

    protected static ?string $title = 'Relatório de Status';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['Super Admin', 'Relatório de Status']);
    }
    public static function getNavigationGroup(): string
    {
        return __('General');
    }

    public function getHeaderWidgets(): array
    {
        return [
            DomainStatusTableWidget::class,
            HostingStatusTableWidget::class
        ];
    }
}
