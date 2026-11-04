<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Full Home Cleaning',
                'icon' => '🧹',
                'color' => '#3B82F6',
                'description' => 'Jasa bersih-bersih rumah, kos, kantor atau kontrakan secara menyeluruh',
            ],
            [
                'name' => 'Basic Daily Cleaning',
                'icon' => '🧽',
                'color' => '#06B6D4',
                'description' => 'Bantuan bersih-bersih harian ringan, mencuci piring, dan merapikan ruangan',
            ],
            [
                'name' => 'Buang Sampah',
                'icon' => '🗑️',
                'color' => '#10B981',
                'description' => 'Bantu angkut dan buang sampah rumah tangga atau kos ke tempat pembuangan',
            ],
            [
                'name' => 'Mengubur Bangkai Hewan',
                'icon' => '🪦',
                'color' => '#64748B',
                'description' => 'Membantu mencari lokasi layak dan proses mengubur bangkai hewan dengan baik',
            ],
            [
                'name' => 'Membersihkan Kandang / Kotoran Hewan',
                'icon' => '🐾',
                'color' => '#F59E0B',
                'description' => 'Membersihkan kotoran atau kandang hewan peliharaan (kucing, anjing, burung, dll)',
            ],
            [
                'name' => 'Antar / Jemput',
                'icon' => '🛵',
                'color' => '#8B5CF6',
                'description' => 'Antar jemput orang, barang, dokumen, makanan, hingga hewan kesayangan',
            ],
            [
                'name' => 'Angkut Barang',
                'icon' => '📦',
                'color' => '#EC4899',
                'description' => 'Bantuan tenaga angkut dan pindahkan barang berat, perabotan, atau logistik',
            ],
            [
                'name' => 'Pindahan',
                'icon' => '🚚',
                'color' => '#F97316',
                'description' => 'Bantu proses pindahan rumah/kos: angkat barang, packing, unpacking, dan beres-beres',
            ],
            [
                'name' => 'SPY / Mata - Mata',
                'icon' => '🕵️',
                'color' => '#334155',
                'description' => 'Layanan investigasi ringan, cek lokasi, dan pemantauan situasi secara rahasia dan aman',
            ],
            [
                'name' => 'SaBa Food',
                'icon' => '🍔',
                'color' => '#EF4444',
                'description' => 'Bantu beliin dan antarkan makanan atau minuman favorit langsung ke lokasimu',
            ],
            [
                'name' => 'Nemenin Olahraga',
                'icon' => '🏃',
                'color' => '#14B8A6',
                'description' => 'Teman olahraga lari, badminton, gym, atau gowes agar lebih semangat dan produktif',
            ],
            [
                'name' => 'Jasa Lainnya',
                'icon' => '✨',
                'color' => '#6B7280',
                'description' => 'Melayani apa saja asalkan halal, tidak kriminal, tidak merugikan orang lain dan masuk akal',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                [
                    'icon' => $category['icon'],
                    'color' => $category['color'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
