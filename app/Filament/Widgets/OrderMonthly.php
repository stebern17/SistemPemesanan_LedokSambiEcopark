<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;

class OrderMonthly extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            // ambil data order
            'datasets' => [
                [
                    'label' => 'Order',
                    'data' => [
                        Order::whereMonth('created_at', '01')->count(),
                        Order::whereMonth('created_at', '02')->count(),
                        Order::whereMonth('created_at', '03')->count(),
                        Order::whereMonth('created_at', '04')->count(),
                        Order::whereMonth('created_at', '05')->count(),
                        Order::whereMonth('created_at', '06')->count(),
                        Order::whereMonth('created_at', '07')->count(),
                    ],
                    'backgroundColor' => '#108482',
                    'borderColor' => '#108482',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
