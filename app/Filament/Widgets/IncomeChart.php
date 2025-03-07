<?php

namespace App\Filament\Widgets;

use App\Models\OrderDetail;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;


class IncomeChart extends ChartWidget
{
    protected static ?string $heading = 'Income';

    public ?string $filter = 'day';

    protected function getData(): array
    {
        if ($this->filter === 'day') {
            // Mengambil data total_amount berdasarkan tanggal
            $data = OrderDetail::select(DB::raw('DATE(order_details.created_at) as date'), DB::raw('SUM(order_details.total_amount) as total'))
                ->join('orders', 'order_details.order_id', '=', 'orders.id') // Join with the orders table
                ->where('orders.is_paid', 1) // Filter for paid orders
                ->groupBy('date')
                ->orderBy('date') // Sort by date
                ->get();

            // Format label untuk tanggal
            $labels = $data->pluck('date')->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('d M Y'); // Format: 01 Jan 2025
            })->toArray();
        } else if ($this->filter === 'month') {
            // Mengambil data total_amount berdasarkan bulan
            $data = OrderDetail::select(DB::raw('TO_CHAR(order_details.created_at, \'YYYY-MM\') as date'), DB::raw('SUM(order_details.total_amount) as total'))
                ->join('orders', 'order_details.order_id', '=', 'orders.id') // Join with the orders table
                ->where('orders.is_paid', 1) // Filter for paid orders
                ->groupBy('date')
                ->orderBy('date') // Sort by month
                ->get();

            // Format label for month
            $labels = $data->pluck('date')->map(function ($date) {
                return \Carbon\Carbon::createFromFormat('Y-m', $date)->format('F Y'); // Format: January 2025
            })->toArray();
        } else {
            // Jika filter tidak valid, kembalikan array kosong
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }

        // Mengambil total amount
        $totals = $data->pluck('total')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Amount per ' . ucfirst($this->filter),
                    'data' => $totals,
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'pointBackgroundColor' => 'rgba(75, 192, 192, 1)',
                    'fill' => true,
                ],
            ],
        ];
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
            'day' => 'Daily',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
