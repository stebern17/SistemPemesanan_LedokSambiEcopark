<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('order_id')
            ->columns([
                Tables\Columns\TextColumn::make('menu.category')
                    ->label('Category')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('menu.name')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('quantity')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('menu.price')
                    ->label('Price')
                    ->alignCenter()
                    ->money('IDR')
                    ->numeric()
                    ->prefix('Rp. '),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Price')
                    ->alignCenter()
                    ->money('IDR')
                    ->numeric()
                    ->prefix('Rp. '),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('printReceipt')
                    ->label('Print Receipt')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function ($livewire) {
                        return $this->printReceipt($livewire->ownerRecord);
                    })
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function printReceipt($order)
    {
        $items = $order->items()->with('menu')->get();
        $total = $items->sum('total_amount');

        $pdf = Pdf::loadView('receipt', [
            'order' => $order,
            'items' => $items,
            'total' => $total,
            'date' => now()->format('d/m/Y H:i:s'),
            'receipt_number' => sprintf('RCP-%s-%s', $order->id, now()->format('YmdHis'))
        ]);

        $fileName = sprintf('Order-%s.pdf', $order->id);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }


    public function isReadOnly(): bool
    {
        return true;
    }
}
