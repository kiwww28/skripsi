<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiResource\Pages;
use App\Filament\Resources\TransaksiResource\RelationManagers;
use App\Models\Product;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationLabel = 'Transactions';
    protected static ?string $pluralModelLabel = 'Transactions';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->options(Product::all()->pluck('nama', 'id'))
                    ->searchable()
                    ->reactive() // Menandakan jika pilihan berubah, harus ada reaksi
                    ->afterStateUpdated(function (callable $set, $state) {
                        // Ketika product_id berubah, set ulang total menjadi 0
                        $set('total', 0);
                    }),

                TextInput::make('jumlah')
                    ->numeric()
                    ->reactive() // Menandakan jika jumlah berubah, harus ada reaksi
                    ->afterStateUpdated(function (callable $set, $state, $get) {
                        // Ketika jumlah berubah, hitung total berdasarkan harga produk
                        $product = Product::find($get('product_id')); // Ambil produk berdasarkan id
                        if ($product) {
                            $total = $product->harga * $state; // Menghitung total (harga * jumlah)
                            $set('total', $total); // Set nilai total ke field 'total'
                        }
                    }),

                TextInput::make('total')
                    ->numeric()
                    ->readOnly(), // Menonaktifkan field total karena akan dihitung otomatis
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.nama'),
                TextColumn::make('jumlah'),
                TextColumn::make('total')->money('IDR', locale: 'id'),
                TextColumn::make('created_at')->date()->label('Tanggal transaksi'),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksis::route('/'),
            'create' => Pages\CreateTransaksi::route('/create'),
            'edit' => Pages\EditTransaksi::route('/{record}/edit'),
        ];
    }
}
