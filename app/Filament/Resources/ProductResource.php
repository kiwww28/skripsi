<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Kategori;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')->required(),
                Forms\Components\TextInput::make('stok')->required()->numeric(),
                Forms\Components\TextInput::make('harga')->required()->numeric(),
                Select::make('kategori_id')
                    ->label('Kategori')
                    ->options(Kategori::pluck('nama', 'id')) // langsung ambil semua
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Produk')
                    ->color(function ($record) {
                        $stokTipis = $record->stok < 20;
                        $laris = $record->isTrending(); // 🔥 tinggal panggil dari model

                        if ($stokTipis && $laris) {
                            return 'warning';
                        } // 🟡 stok tipis tapi laku keras
                        if ($laris) {
                            return 'success';
                        }                // 🟢 laku tapi stok aman
                        if ($stokTipis) {
                            return 'default';
                        }             // 🔴 stok tipis tapi gak laku

                        return null;
                    }),
                TextColumn::make('stok'),
                TextColumn::make('harga')->money('IDR', locale: 'id'),
                TextColumn::make('kategori.nama'),
            ])
            ->filters([])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
