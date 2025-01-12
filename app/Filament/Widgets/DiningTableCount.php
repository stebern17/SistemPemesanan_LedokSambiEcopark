<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\DiningTable;

class DiningTableCount extends BaseWidget
{

    protected function getStats(): array
    {
        $availableTables = DiningTable::where('status', 'available')->count();
        return [
            Stat::make('Available Tables', $availableTables),
            Stat::make('Occupied Tables', DiningTable::where('status', 'unavailable')->count()),
            Stat::make('Total Tables', DiningTable::count()),
        ];
    }
}
