<?php

namespace App\Filament\Widgets;

use App\Models\Domain;
use App\Models\Status;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class DomainStatusChart extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['Super Admin', 'Painel de Controle']);
    }
    public function getHeading(): string|Htmlable|null
    {
        return __('Domain Status');
    }

    protected function getData(): array
    {
        $status = [
            'financial_informed' => 0,
            'charge_sent' => 0,
            'waiting_payment' => 0,
            'paid' => 0,
            'dont_renew' => 0
        ];
        $labels = [];
        $data = [];
        $background = [
            "rgb(45, 85, 255)",
            "rgb(72, 114, 255)",
            "rgb(99, 143, 255)",
            "rgb(126, 172, 255)",
            "rgb(153, 201, 255)",
            "rgb(0, 155, 119)",
            "rgb(28, 175, 137)",
            "rgb(56, 195, 155)",
            "rgb(84, 215, 173)",
            "rgb(112, 235, 191)",
            "rgb(132, 94, 194)",
            "rgb(153, 120, 204)",
            "rgb(174, 146, 214)",
            "rgb(195, 172, 224)",
            "rgb(216, 198, 234)",
            "rgb(255, 167, 81)",
            "rgb(255, 182, 109)",
            "rgb(255, 197, 137)",
            "rgb(255, 212, 165)",
            "rgb(255, 227, 193)",
            "rgb(87, 87, 87)",
            "rgb(120, 120, 120)",
            "rgb(160, 160, 160)",
            "rgb(200, 200, 200)",
            "rgb(230, 230, 230)",
        ];
        foreach(Domain::all() as $domain){
            if($domain->status){
                $status[$domain->status] += 1;
            }
        }
        foreach($status as $status_name => $status_count){
            $labels[] = __($status_name);
            $data[] = $status_count;
        }

        return [
            "labels" => $labels,
            "datasets" => [[
                "label" => 'Status',
                "data" => $data,
                "backgroundColor" => $background,
            ]]
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
