<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Menu;
use App\Models\OrderDetail;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Midtrans\Config;
use Midtrans\Snap;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }



    protected function beforeCreate(): void
    {
        $data = $this->data;

        if (!$data['cashless']) {
            if ($data['received_amount'] < $data['grand_total']) {
                Notification::make()
                    ->danger()
                    ->title('Cannot create order')
                    ->body('The received amount must be equal to or greater than the total amount.')
                    ->send();

                $this->halt();
            }
        }
    }

    protected function afterCreate(): void
    {

        Notification::make()
            ->success()
            ->title('Order Created')
            ->body('Change amount: Rp.' . number_format($this->data['change_amount'], 0, ',', '.'))
            ->send();


        Notification::make()
            ->success()
            ->title('Order Created')
            ->body('Change amount: Rp.' . number_format($this->data['change_amount'], 0, ',', '.'))
            ->sendToDatabase(User::where('role', 'admin')->get());
    }

    protected function getRedirectUrl(): string
    {
        $data = $this->data;

        if (!$data['cashless']) {
            return parent::getRedirectUrl();
        }

        $transactionDetails = [
            'order_id' => $this->record->unique_id,
            'gross_amount' => $data['grand_total'],
        ];


        $dbOrderDetails = OrderDetail::with('menu')->where('order_id', $this->record->id)->get();

        $itemsDetails = [];

        foreach ($dbOrderDetails as $item) {
            $itemsDetails[] = [
                'id' => $item->menu_id,
                'name' => $item->menu->name,
                'price' => $item->price,
                'quantity' => $item->quantity
            ];
        }

        $paymentUrl = Snap::createTransaction([
            'transaction_details' => $transactionDetails,
            'item_details' => $itemsDetails,
        ])->redirect_url;

        return $paymentUrl;
    }
}
