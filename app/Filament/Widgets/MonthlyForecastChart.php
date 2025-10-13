<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Widgets\ChartWidget;

class MonthlyForecastChart extends ChartWidget
{
    protected static ?string $heading = 'Forecast Penjualan Bulanan';

    protected function getData(): array
    {
        $sales = Transaksi::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as bulan, SUM(jumlah) as total')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        $labels = $sales->keys()->toArray();
        $values = $sales->values()->toArray();

        // Hitung rata-rata growth buat prediksi bulan depan
        $growths = [];
        for ($i = 1; $i < count($values); $i++) {
            $growths[] = ($values[$i] - $values[$i - 1]) / max($values[$i - 1], 1);
        }
        $avgGrowth = collect($growths)->avg() ?? 0;
        $forecast = end($values) * (1 + $avgGrowth);

        // Tambahkan bulan berikutnya ke chart
        $nextMonth = now()->addMonth()->format('Y-m');
        $labels[] = $nextMonth;
        $values[] = round($forecast);

        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan',
                    'data' => $values,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
