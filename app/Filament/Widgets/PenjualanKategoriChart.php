<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Widgets\ChartWidget;

class PenjualanKategoriChart extends ChartWidget
{
    protected static ?string $heading = 'Penjualan per Kategori';

    protected function getData(): array
    {
        // Ambil total penjualan per kategori lewat relasi
        $penjualanPerKategori = Transaksi::with('product.kategori')
            ->get()
            ->groupBy(fn ($trx) => $trx->product?->kategori?->nama ?? 'Tanpa Kategori')
            ->map(fn ($group) => $group->sum('total'));

        $labels = $penjualanPerKategori->keys();
        $data = $penjualanPerKategori->values();

        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan',
                    'data' => $data,
                    'backgroundColor' => [
                        '#4ade80', '#60a5fa', '#fbbf24', '#f87171', '#a78bfa',
                        '#f472b6', '#34d399', '#818cf8',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie'; // bisa diganti 'bar' atau 'doughnut'
    }
}
