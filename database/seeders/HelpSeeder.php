<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\City;
use App\Models\Help;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class HelpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Customer references
        $dewi = User::where('email', 'dewi.jogja@test.com')->first();
        $budiSleman = User::where('email', 'budi.sleman@test.com')->first();
        $sitiJkt = User::where('email', 'customer@sayabantu.com')->first();
        $budiJkt = User::where('email', 'budi@example.com')->first();

        // City references
        $jogja = City::where('name', 'like', '%Kota Yogyakarta%')->first() ?: City::find(31);
        $sleman = City::where('name', 'like', '%Sleman%')->first() ?: City::find(32);
        $jkt = City::where('name', 'like', '%Jakarta Pusat%')->first() ?: City::find(1);

        $jogjaId = $jogja ? $jogja->id : 31;
        $slemanId = $sleman ? $sleman->id : 32;
        $jktId = $jkt ? $jkt->id : 1;

        $catRumah = Category::where('name', 'like', '%Cleaning%')->orWhere('name', 'like', '%Rumah%')->first() ?: Category::first();
        $catKesehatan = Category::where('name', 'like', '%Hewan%')->orWhere('name', 'like', '%Kesehatan%')->first() ?: Category::first();
        $catTransport = Category::where('name', 'like', '%Antar%')->orWhere('name', 'like', '%Transportasi%')->first() ?: Category::first();
        $catPangan = Category::where('name', 'like', '%Food%')->orWhere('name', 'like', '%Pangan%')->first() ?: Category::first();

        $custFeePercent = (float) AppSetting::get('customer_service_fee_percent', 10);
        $mitraFeePercent = (float) AppSetting::get('mitra_platform_fee_percent', 10);

        // Bantuan spesifik yang realistis & berada dalam radius mitra terdekat (<10km)
        $curatedHelps = [];

        if ($dewi) {
            $curatedHelps[] = [
                'user_id' => $dewi->id,
                'city_id' => $jogjaId,
                'category_id' => $catRumah?->id ?? 1,
                'title' => 'Bantuan Perbaikan Atap Rumah Bocor di Gondokusuman',
                'description' => 'Ada genteng yang bergeser dan bocor saat hujan deras di kamar depan. Butuh bantuan naik ke atap untuk membetulkan posisi genteng dan menambal retakan.',
                'amount' => 50000,
                'location' => 'Dekat Toko ELS Computer, Terban',
                'full_address' => 'Jl. C. Simanjuntak No. 25, Terban, Gondokusuman, Kota Yogyakarta',
                'latitude' => -7.7833,
                'longitude' => 110.3833,
                'scheduled_at' => now()->addDay()->setTime(9, 30),
            ];

            $curatedHelps[] = [
                'user_id' => $dewi->id,
                'city_id' => $jogjaId,
                'category_id' => $catKesehatan?->id ?? 3,
                'title' => 'Bantuan Pengantaran Obat & Makanan untuk Orang Tua',
                'description' => 'Mohon bantuan mitra untuk mengantarkan paket obat dan makanan bernutrisi ke rumah orang tua saya di Danurejan. Saya sedang dinas kerja.',
                'amount' => 35000,
                'location' => 'Dekat Stasiun Lempuyangan, Danurejan',
                'full_address' => 'Jl. Bausasran No. 14, Danurejan, Kota Yogyakarta',
                'latitude' => -7.7950,
                'longitude' => 110.3750,
                'scheduled_at' => now()->addDays(2)->setTime(14, 0),
            ];

            $curatedHelps[] = [
                'user_id' => $dewi->id,
                'city_id' => $jogjaId,
                'category_id' => $catRumah?->id ?? 1,
                'title' => 'Bantuan Angkut Pindahan Lemari & Kasur',
                'description' => 'Perlu bantuan 1 orang mitra berbadan kuat untuk membantu memindahkan kasur springbed dan lemari pakaian dari lantai 1 ke lantai 2.',
                'amount' => 75000,
                'location' => 'Gang Masjid Kotabaru, Gondokusuman',
                'full_address' => 'Jl. Kotabaru No. 12, Gondokusuman, Kota Yogyakarta',
                'latitude' => -7.7850,
                'longitude' => 110.3720,
                'scheduled_at' => now()->addDays(1)->setTime(10, 0),
            ];
        }

        if ($budiSleman) {
            $curatedHelps[] = [
                'user_id' => $budiSleman->id,
                'city_id' => $slemanId,
                'category_id' => $catRumah?->id ?? 1,
                'title' => 'Perbaikan Pipa Saluran Cuci Piring Bocor',
                'description' => 'Pipa pembuangan wastafel dapur bocor dan air merembes ke lantai. Sudah ada lem pipa dan isolasi, butuh bantuan untuk memasangnya kembali.',
                'amount' => 60000,
                'location' => 'Dekat Kampus UNY / Gejayan, Caturtunggal',
                'full_address' => 'Jl. Gejayan Gang Guru No. 8, Caturtunggal, Depok, Sleman',
                'latitude' => -7.7650,
                'longitude' => 110.3890,
                'scheduled_at' => now()->addDay()->setTime(11, 0),
            ];
        }

        if ($sitiJkt) {
            $curatedHelps[] = [
                'user_id' => $sitiJkt->id,
                'city_id' => $jktId,
                'category_id' => $catTransport?->id ?? 5,
                'title' => 'Antar Berkas Administrasi ke Kantor Pos Fatmawati',
                'description' => 'Butuh bantuan mengantarkan amplop berkas penting ke kantor pos atau ekspedisi terdekat karena saya sedang sakit di rumah.',
                'amount' => 50000,
                'location' => 'Depan Stasiun Gambir',
                'full_address' => 'Jl. Kebon Sirih No. 12, Gambir, Jakarta Pusat',
                'latitude' => -6.1865,
                'longitude' => 106.8340,
                'scheduled_at' => now()->addDay()->setTime(13, 0),
            ];
        }

        foreach ($curatedHelps as $item) {
            $amt = (float) $item['amount'];
            $cFee = round($amt * $custFeePercent / 100);
            $total = $amt + $cFee;
            $mFee = round($amt * $mitraFeePercent / 100);
            $netM = $amt - $mFee;

            Help::updateOrCreate(
                [
                    'user_id' => $item['user_id'],
                    'title' => $item['title'],
                ],
                [
                    'order_id' => 'HLP-'.strtoupper(substr(md5($item['title'].$item['user_id']), 0, 8)),
                    'help_type' => 'scheduled',
                    'category_id' => $item['category_id'],
                    'city_id' => $item['city_id'],
                    'amount' => $amt,
                    'base_amount' => $amt,
                    'customer_fee_percent' => $custFeePercent,
                    'customer_fee_amount' => $cFee,
                    'total_customer_paid' => $total,
                    'mitra_fee_percent' => $mitraFeePercent,
                    'mitra_fee_amount' => $mFee,
                    'net_mitra_amount' => $netM,
                    'admin_fee' => 0,
                    'total_amount' => $total,
                    'description' => $item['description'],
                    'location' => $item['location'],
                    'full_address' => $item['full_address'],
                    'scheduled_at' => $item['scheduled_at'],
                    'latitude' => $item['latitude'],
                    'longitude' => $item['longitude'],
                    'status' => 'menunggu_mitra',
                    'mitra_id' => null,
                ]
            );
        }
    }
}
