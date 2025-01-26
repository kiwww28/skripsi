<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecommendOrders extends BaseWidget
{
    protected static ?string $heading = 'Wajib Restock';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::where('stok', '<=', 10)
            )
            ->columns([
                TextColumn::make('nama'),
                TextColumn::make('stok')->label('Sisa stok')
            ]);
    }
}
