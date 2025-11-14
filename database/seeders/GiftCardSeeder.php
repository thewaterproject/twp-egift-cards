<?php

namespace Database\Seeders;

use App\Models\GiftCard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder for GiftCard model.
 *
 * @package Database\Seeders
 * @since 1.0.0
 */
class GiftCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @since 1.0.0
     */
    public function run(): void
    {
        GiftCard::create([
            'gift_card_id' => 'Y4R89RV',
            'status' => 'pending',
            'created_at' => '2025-11-14 19:04:06',
            'occasion' => [
                'type' => 'completely-different',
                'display_name' => 'A Special Occasion',
            ],
            'recipient' => [
                'first_name' => 'peter',
                'last_name' => 'chasse',
                'email' => 'peter@thewaterproject.org',
                'full_name' => 'peter chasse',
            ],
            'sender' => [
                'first_name' => 'Chris',
                'last_name' => 'Carvache',
                'email' => 'chris@thewaterproject.org',
                'display_name' => 'Chris Carvache',
                'hide_name' => false,
                'custom_name' => null,
            ],
            'delivery' => [
                'date' => '2025-11-14',
                'send_immediately' => true,
                'formatted_date' => 'sent immediately',
            ],
            'gift' => [
                'amount' => 25,
                'currency' => 'USD',
                'formatted_amount' => '$25',
            ],
            'personalization' => [
                'message' => '',
                'photo' => 'https://res.cloudinary.com/the-water-project/image/upload/c_fill,g_auto,dpr_auto,q_auto:eco,f_auto,w_150,h_150/site/celebrate_water_2018.jpg',
                'in_memory_of' => null,
                'memorial_type' => 'memory',
                'in_recognition_of' => 'Artificial Intelligence',
            ],
            'redemption' => [
                'code' => 'Y4R89RV',
                'url' => 'https://thewaterproject.org/redeem/Y4R89RV',
            ],
            'checkout' => [
                'url' => 'https://thewaterproject.org/?form=eGiftCard&amount=25&egift_id=Y4R89RV&firstName=Chris&lastName=Carvache&email=chris%40thewaterproject.org&modifyAmount=no',
                'amount' => 25,
            ],
            'recipient_email' => 'peter@thewaterproject.org',
            'sender_email' => 'chris@thewaterproject.org',
            'amount' => 25,
            'currency' => 'USD',
            'delivery_date' => '2025-11-14',
            'send_immediately' => true,
        ]);
    }
}
