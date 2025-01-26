<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Faker\Provider\ar_EG\Text;
use Filament\Actions;
use Filament\Forms\Components\Component;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Grid::make()
                    ->extraAttributes(['class' => 'bg-white shadow-lg  rounded-lg p-4'])
                    ->schema([
                        TextEntry::make('total_amount')
                            ->label('Total Amount')
                            ->prefix('Rp. ')
                            ->money('IDR')
                            ->badge()
                            ->color('primary1')
                            ->numeric(),

                        IconEntry::make('is_paid')
                            ->label('Paid')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle'),

                        TextEntry::make('diningTable.number')
                            ->label('Table Number')
                            ->badge()
                            ->color('primary1')
                            ->numeric(),

                        TextEntry::make('diningTable.position')
                            ->label('Table Location')
                            ->formatStateUsing(fn($state) => ucfirst($state))
                            ->badge()
                            ->color('primary1'),
                    ]),


            ]);
    }
}
