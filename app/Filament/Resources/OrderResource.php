<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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
                        Forms\Components\Select::make('menu_id')
                            ->relationship('menu', 'name') // Corrected relationship
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Fetch the price of the selected menu and set it
                                $menu = \App\Models\Menu::find($state);
                                $set('price', $menu ? $menu->price : 0);
                            }),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->reactive() // Make it reactive to trigger updates
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // Update the total price based on quantity and menu price
                                $pricePerItem = $get('price');
                                $set('total_amount', $pricePerItem * $state);
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
                    ->addActionLabel('Add Menu'),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('DiningTable.number')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('DiningTable.position')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->label('Location'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(fn($state) => match ($state) {
                        'waiting' => 'primary',
                        'served' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('idr')
                    ->sortable(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
