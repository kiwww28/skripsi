<?php

// App\Models\Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function isTrending(): bool
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;
        $bulanLalu = now()->subMonth()->month;
        $tahunLalu = now()->subMonth()->year;

        // total terjual bulan ini & bulan lalu
        $terjualBulanIni = $this->transaksi()
            ->whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->sum('jumlah');

        $terjualBulanLalu = $this->transaksi()
            ->whereMonth('created_at', $bulanLalu)
            ->whereYear('created_at', $tahunLalu)
            ->sum('jumlah');

        // threshold dinamis: >= 5% total penjualan bulan ini
        $totalUnitBulanIni = Transaksi::whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->sum('jumlah');
        $threshold = $totalUnitBulanIni * 0.05;

        return $terjualBulanIni > $terjualBulanLalu && $terjualBulanIni >= $threshold;
    }
}
