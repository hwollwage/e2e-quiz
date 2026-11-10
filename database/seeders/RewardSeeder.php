<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        $rewards = [
            // suplemen
            ['name' => 'Whey Protein 1kg', 'description' => 'Suplemen protein untuk recovery otot pasca latihan', 'point_cost' => 800, 'category' => 'suplemen', 'is_featured' => true, 'stock' => 20, 'image' => 'images/rewards/wheyprotein.jpeg'],
            ['name' => 'BCAA 200g', 'description' => 'Asam amino rantai cabang untuk mencegah katabolisme otot', 'point_cost' => 500, 'category' => 'suplemen', 'is_featured' => false, 'stock' => 15, 'image' => 'images/rewards/bag.jpeg'],
            ['name' => 'Creatine Monohydrate 300g', 'description' => 'Meningkatkan performa dan kekuatan saat latihan intensitas tinggi', 'point_cost' => 600, 'category' => 'suplemen', 'is_featured' => false, 'stock' => 10, 'image' => 'images/rewards/creatine.jpeg'],

            // accesori
            ['name' => 'Gym Gloves', 'description' => 'Sarung tangan gym anti slip untuk grip lebih kuat', 'point_cost' => 400, 'category' => 'aksesoris', 'is_featured' => false, 'stock' => 25, 'image' => 'images/rewards/gloves.jpeg'],
            ['name' => 'Lifting Belt', 'description' => 'Sabuk angkat beban untuk proteksi punggung bawah', 'point_cost' => 700, 'category' => 'aksesoris', 'is_featured' => true, 'stock' => 10, 'image' => 'images/rewards/bag.jpeg'],
            ['name' => 'Resistance Band Set', 'description' => 'Set 5 resistance band berbagai tingkat ketahanan', 'point_cost' => 450, 'category' => 'aksesoris', 'is_featured' => false, 'stock' => 20, 'image' => 'images/rewards/tshirt.jpeg'],

            // diskon
            ['name' => 'Diskon Membership 50%', 'description' => 'Potongan harga 50% untuk perpanjangan membership 1 bulan', 'point_cost' => 1000, 'category' => 'diskon', 'is_featured' => true, 'stock' => 5, 'image' => 'images/rewards/tshirt.jpeg'],
            ['name' => 'Diskon Personal Trainer 1 Sesi', 'description' => 'Gratis 1 sesi personal trainer 60 menit', 'point_cost' => 900, 'category' => 'diskon', 'is_featured' => false, 'stock' => 8, 'image' => 'images/rewards/bag.jpeg'],

            // merchandise
            ['name' => 'Gym T-Shirt Exclusive', 'description' => 'Kaos olahraga dry-fit merchandise official gym', 'point_cost' => 600, 'category' => 'merchandise', 'is_featured' => false, 'stock' => 15, 'image' => 'images/rewards/tshirt.jpeg'],
            ['name' => 'Gym Bag', 'description' => 'Tas gym kapasitas 30L dengan kompartemen sepatu', 'point_cost' => 850, 'category' => 'merchandise', 'is_featured' => true, 'stock' => 10, 'image' => 'images/rewards/bag.jpeg'],
        ];

        foreach ($rewards as $reward) {
            Reward::updateOrCreate(
                ['name' => $reward['name']], array_merge($reward, ['is_active' => true])
            );
        }
    }
}
