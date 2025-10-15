<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun admin
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('admin123'),
        ]);

        // Daftar kategori
        $kategoris = [
            ['nama' => 'Elektronik', 'deskripsi' => 'Produk elektronik seperti ponsel, laptop, dan perangkat lainnya.'],
            ['nama' => 'Fashion', 'deskripsi' => 'Pakaian, sepatu, dan aksesoris terkini.'],
            ['nama' => 'Makanan', 'deskripsi' => 'Berbagai jenis makanan siap saji dan bahan makanan.'],
            ['nama' => 'Minuman', 'deskripsi' => 'Minuman ringan, kopi, teh, dan minuman kesehatan.'],
            ['nama' => 'Kesehatan', 'deskripsi' => 'Obat, suplemen, dan perlengkapan medis.'],
            ['nama' => 'Olahraga', 'deskripsi' => 'Peralatan dan pakaian olahraga.'],
            ['nama' => 'Otomotif', 'deskripsi' => 'Aksesori dan perlengkapan kendaraan bermotor.'],
            ['nama' => 'Rumah Tangga', 'deskripsi' => 'Perabot dan perlengkapan rumah tangga.'],
            ['nama' => 'Kecantikan', 'deskripsi' => 'Produk perawatan kulit dan kosmetik.'],
        ];

        foreach ($kategoris as $kategoriData) {
            $kategori = Kategori::create($kategoriData);

            // Tentuin produk dan range harga yang realistis
            [$produkList, $harga] = match ($kategori->nama) {
                'Elektronik' => [
                    ['Smartphone X', 'Laptop Pro 15', 'Earphone Wireless'],
                    1500000,
                ],
                'Fashion' => [
                    ['Kaos Polos', 'Sepatu Sneakers', 'Topi Trucker'],
                    50000,
                ],
                'Makanan' => [
                    ['Nasi Goreng Instan', 'Keripik Pedas', 'Mie Kering Premium'],
                    10000,
                ],
                'Minuman' => [
                    ['Kopi Arabica', 'Teh Hijau Botol', 'Air Mineral Premium'],
                    5000,
                ],
                'Kesehatan' => [
                    ['Vitamin C 1000mg', 'Masker Medis', 'Obat Batuk Herbal'],
                    20000,
                ],
                'Olahraga' => [
                    ['Bola Sepak', 'Matras Yoga', 'Sarung Tangan Gym'],
                    100000,
                ],
                'Otomotif' => [
                    ['Oli Mesin', 'Helm Full Face', 'Sarung Jok Motor'],
                    80000,
                ],
                'Rumah Tangga' => [
                    ['Sapu Modern', 'Kain Pel Serbaguna', 'Dispenser Air'],
                    20000,
                ],
                'Kecantikan' => [
                    ['Face Wash', 'Lipstik Matte', 'Serum Wajah'],
                    30000,
                ],
                default => [['Produk 1', 'Produk 2', 'Produk 3'], 10000],
            };

            foreach ($produkList as $produkNama) {
                Product::create([
                    'kategori_id' => $kategori->id,
                    'nama' => $produkNama,
                    'stok' => rand(10, 100),
                    'harga' => $harga,
                ]);
            }
        }
    }
}
