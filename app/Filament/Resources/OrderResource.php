<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Models\Order;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\Filter;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'hugeicons-shopping-cart-02';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Repeater::make('items')
                    ->relationship('items')
                    ->label('Menu')
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->options(
                                fn() => \App\Models\Menu::select('category')
                                    ->distinct()
                                    ->pluck('category', 'category')
                                    ->mapWithKeys(fn($category) => [$category => ucfirst($category)])
                            )
                            ->label('Category')
                            ->required()
                            ->reactive(),
                        Forms\Components\Select::make('menu_id')
                            ->relationship('menu', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->options(function (callable $get) {
                                $category = $get('category');
                                return \App\Models\Menu::where('category', $category)
                                    ->pluck('name', 'id');
                            })
                            ->afterStateUpdated(function ($state, callable $set) {
                                $menu = \App\Models\Menu::find($state);
                                $set('price', $menu ? $menu->price : 0);
                            }),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $pricePerItem = $get('price') ?? 0;
                                $set('total_amount', $pricePerItem * $state);

                                // Hitung ulang grand_total setiap kali quantity berubah
                                $items = $get('../../items'); // Akses semua item di Repeater
                                $total = collect($items)->sum('total_amount');
                                $set('../../grand_total', $total); // Update grand_total
                            }),
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('Rp.')
                            ->readOnly(),
                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->prefix('Rp.')
                            ->readOnly(),
                    ])
                    ->addActionLabel('Add Menu')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $total = collect($state)->sum('total_amount');
                        $set('grand_total', $total); // Update grand_total saat item Repeater berubah
                    }),

                Forms\Components\Select::make('dining_table_id')
                    ->relationship('diningTable', 'number')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'waiting' => 'Waiting',
                        'served' => 'Served',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('grand_total')
                    ->label('Total Payment')
                    ->prefix('Rp.')
                    ->disabled()
                    ->numeric()
                    ->dehydrated(fn($state) => $state !== null),

                Forms\Components\Section::make('Payment')
                    ->schema([
                        Forms\Components\TextInput::make('received_amount')
                            ->label('Amount Received')
                            ->prefix('Rp.')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $grandTotal = $get('grand_total') ?? 0;
                                if ($state < $grandTotal) {
                                    Notification::make()
                                        ->warning()
                                        ->title('Warning')
                                        ->body('Amount received is less than total amount')
                                        ->send();
                                }
                                $change = max(0, $state - $grandTotal);
                                $set('change_amount', $change);
                                $set('is_paid', $state >= $grandTotal);
                            }),

                        Forms\Components\TextInput::make('change_amount')
                            ->label('Change')
                            ->prefix('Rp.')
                            ->disabled()
                            ->numeric(),

                        Forms\Components\Hidden::make('is_paid')
                            ->default(true),
                    ])
                    ->collapsed(false),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order ID')
                    ->alignCenter()
                    ->numeric()
                    ->searchable(),
                Tables\Columns\TextColumn::make('DiningTable.number')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('DiningTable.position')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->label('Location')
                    ->alignCenter(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'waiting' => 'Waiting',
                        'served' => 'Served',
                        'canceled' => 'Canceled',
                    ])
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Paid')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('idr')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('Waiting Orders')
                    ->query(fn(Builder $query) => $query->where('status', '=', 'waiting'))
                    ->default(),
                Filter::make('Served Orders')
                    ->query(fn(Builder $query) => $query->where('status', '=', 'served')),
                Filter::make('Canceled Orders')
                    ->query(fn(Builder $query) => $query->where('status', '=', 'canceled')),
            ])
            ->actions([
                ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => !$record->is_paid)
                    ->label('Pay Now')
                    ->icon('heroicon-s-currency-dollar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function afterSave($record): void
    {
        // Jika ini adalah edit, hitung grand_total dari items
        if ($record->wasChanged('items')) {
            $totalAmount = $record->items->sum('total_amount');
            $record->grand_total = $totalAmount;
            $record->saveQuietly();
        }
    }
}
