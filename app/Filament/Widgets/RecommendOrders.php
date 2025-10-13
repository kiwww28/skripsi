<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecommendOrders extends BaseWidget
{
    protected static ?string $heading = 'Wajib Restock (Terlaris di bulan ini)';

    public function table(Table $table): Table
    {
        // ambil semua produk yg stoknya <= 10
        $allProducts = Product::where('stok', '<=', 20)->get();

        // filter yg trending
        $trendingIds = $allProducts
            ->filter(fn ($product) => $product->isTrending())
            ->pluck('id')
            ->toArray();

        // bikin query dari id hasil filter
        $query = Product::query()->whereIn('id', $trendingIds);

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('nama')->label('Nama Produk'),
                TextColumn::make('stok')->label('Sisa Stok'),
                TextColumn::make('kategori.nama')->label('Kategori'),
            ]);
    }
}
