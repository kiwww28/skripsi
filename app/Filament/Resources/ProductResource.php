<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Kategori;
use App\Models\Product;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

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
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;
        $bulanLalu = now()->subMonth()->month;
        $tahunLalu = now()->subMonth()->year;

        // 🔹 Jumlah unit terjual per produk bulan ini
        $terjualBulanIni = Transaksi::select('product_id', DB::raw('SUM(jumlah) as total_jumlah'))
            ->whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->groupBy('product_id')
            ->pluck('total_jumlah', 'product_id');

        // 🔹 Jumlah unit terjual per produk bulan lalu
        $terjualBulanLalu = Transaksi::select('product_id', DB::raw('SUM(jumlah) as total_jumlah'))
            ->whereMonth('created_at', $bulanLalu)
            ->whereYear('created_at', $tahunLalu)
            ->groupBy('product_id')
            ->pluck('total_jumlah', 'product_id');

        // 🔹 Total semua unit terjual bulan ini
        $totalUnitBulanIni = $terjualBulanIni->sum();

        // 🔹 Threshold: produk dengan kontribusi ≥5% total penjualan dianggap “laris”
        $threshold = $totalUnitBulanIni * 0.05;

        // 🔹 Produk laris = penjualan naik & signifikan
        $produkLaris = collect($terjualBulanIni)
            ->filter(function ($jumlah, $productId) use ($terjualBulanLalu, $threshold) {
                $jumlahLalu = $terjualBulanLalu[$productId] ?? 0;
                return $jumlah > $jumlahLalu && $jumlah >= $threshold;
            })
            ->keys()
            ->toArray();

        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Produk')
                    ->color(function ($record) use ($produkLaris) {
                        $stokTipis = $record->stok < 20;
                        $laris = in_array($record->id, $produkLaris);

                        if ($stokTipis && $laris) {
                            return 'warning'; // 🟡 stok tipis tapi laku keras
                        }

                        return null; // ⚪ default putih
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
