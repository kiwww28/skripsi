<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PendapatanChart extends ChartWidget
{
    protected static ?string $heading = 'Chart Pendapatan';

    protected function getData(): array
    {
        // Ambil data transaksi per bulan
        $pendapatanPerBulan = Transaksi::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total) as total_per_month')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Persiapkan data untuk chart
        $labels = [];
        $data = [];

        // Loop untuk menyusun data chart
        foreach ($pendapatanPerBulan as $transaksi) {
            // Menggunakan Carbon untuk format nama bulan
            $labels[] = Carbon::createFromDate($transaksi->year, $transaksi->month, 1)->format('M Y');  // Format 'Jan 2025', 'Feb 2025'
            $data[] = $transaksi->total_per_month;  // Total pendapatan per bulan
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendapatan',
                    'data' => $data, // Data pendapatan per bulan
                ],
            ],
            'labels' => $labels, // Label bulan yang ditampilkan di chart
        ];
    }

    protected function getType(): string
    {
        return 'line';  // Jenis chart, bisa 'line', 'bar', dll.
    }
}
