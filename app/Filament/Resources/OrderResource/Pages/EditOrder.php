<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeEdit(): void
    {
        $data = $this->data;

        if ($data['received_amount'] < $data['grand_total']) {
            Notification::make()
                ->danger()
                ->title('Cannot create order')
                ->body('The received amount must be equal to or greater than the total amount.')
                ->send();

            $this->halt();
        }
    }

    protected function afterEdit(): void
    {
        Notification::make()
            ->success()
            ->title('Order Created')
            ->body('Change amount: Rp.' . number_format($this->data['change_amount'], 0, ',', '.'))
            ->send();
    }
}
