<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiningTableResource\Pages;
use App\Filament\Resources\DiningTableResource\RelationManagers;
use App\Models\DiningTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Pest\Laravel\options;

class DiningTableResource extends Resource
{
    protected static ?string $model = DiningTable::class;

    protected static ?string $navigationIcon = 'hugeicons-dining-table';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Available',
                        'unavailable' => 'Unavailable',
                    ])
                    ->required(),
                Forms\Components\Select::make('position')
                    ->label('Position')
                    ->options([
                        'pendopo' => 'Pendopo',
                        'timur sungai' => 'Timur Sungai',
                        'barat sungai' => 'Barat Sungai',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'available' => 'Available',
                        'unavailable' => 'Unavailable',
                    ])
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('position')
                    ->formatStateUsing(fn($state) => ucfirst($state))
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
            ->defaultSort('number', 'asc')
            ->filters([])
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
            'index' => Pages\ListDiningTables::route('/'),
            'create' => Pages\CreateDiningTable::route('/create'),
            'edit' => Pages\EditDiningTable::route('/{record}/edit'),
        ];
    }
}
