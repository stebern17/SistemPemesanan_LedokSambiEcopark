<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use Filament\Support\RawJs;

use function Pest\Laravel\options;

class OrderMonthly extends ChartWidget
{
    protected static ?string $heading = 'Order Monthly';

    public ?string $filter = 'month';

    protected function getData(): array
    {
        // buatkan kondisi filter
        if ($this->filter === 'month') {
            $data = [
                'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                'datasets' => [
                    [
                        'label' => 'Order',
                        'data' => [
                            Order::whereMonth('created_at', 1)->where('status', 'served')->count(),
                            Order::whereMonth('created_at', 2)->where('status', 'served')->count(),
                            Order::whereMonth('created_at', 3)->where('status', 'served')->count(),
                            Order::whereMonth('created_at', 4)->where('status', 'served')->count(),
                            Order::whereMonth('created_at', 5)->where('status', 'served')->count(),
                            Order::whereMonth('created_at', 6)->where('status', 'served')->count(),
                            Order::whereMonth('created_at', 7)->where('status', 'served')->count(),
                        ],
                        'backgroundColor' => '#108482',
                        'borderColor' => '#108482',
                    ],
                ],
            ];
        } else {
            $data = [
                'labels' => ['2025', '2026', '2027'],
                'datasets' => [
                    [
                        'label' => 'Order',
                        'data' => [
                            Order::whereYear('created_at', 2025)->where('status', 'served')->count(),
                            Order::whereYear('created_at', 2026)->where('status', 'served')->count(),
                            Order::whereYear('created_at', 2027)->where('status', 'served')->count(),
                        ],
                        'backgroundColor' => '#108482',
                        'borderColor' => '#108482',
                    ],
                ],
            ];
        }

        return $data;
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'month' => 'Monthly',
            'year' => 'Yearly'
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
