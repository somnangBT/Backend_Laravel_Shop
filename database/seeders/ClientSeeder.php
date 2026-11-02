<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $clients = [
            [
                'name' => 'Sokha Chan',
                'email' => 'sokha.chan@gmail.com',
                'phone' => '012 345 678',
                'address' => 'Phnom Penh, Toul Kork',
                'created_at' => $now->copy(),
            ],
            [
                'name' => 'Dara Meng',
                'email' => 'dara.meng@gmail.com',
                'phone' => '098 765 432',
                'address' => 'Siem Reap, Svay Dangkum',
                'created_at' => $now->copy()->subDays(2),
            ],
            [
                'name' => 'Vannak Keo',
                'email' => 'vannak.keo@gmail.com',
                'phone' => '077 112 233',
                'address' => 'Battambang, Sangke',
                'created_at' => $now->copy()->subDays(10),
            ],
            [
                'name' => 'Sreynich Pov',
                'email' => 'sreynich.pov@gmail.com',
                'phone' => '089 998 877',
                'address' => 'Kandal, Takhmao',
                'created_at' => $now->copy()->subMonths(2),
            ],
            [
                'name' => 'Borey Heng',
                'email' => 'borey.heng@gmail.com',
                'phone' => '015 443 322',
                'address' => 'Phnom Penh, Chamkarmon',
                'created_at' => $now->copy()->subMonths(6),
            ],
        ];

        foreach ($clients as $c) {
            Client::firstOrCreate(['email' => $c['email']], $c);
        }

        // Also seed a couple sample orders tied to customer emails for realistic metrics
        $sokha = Client::where('email', 'sokha.chan@gmail.com')->first();
        if ($sokha && Order::where('email', $sokha->email)->count() === 0) {
            Order::create([
                'user_id' => 1,
                'email' => $sokha->email,
                'phone' => $sokha->phone,
                'shipping_address' => $sokha->address,
                'subtotal' => 120.00,
                'tax' => 0,
                'shipping' => 2.00,
                'discount' => 5.00,
                'total' => 117.00,
                'status' => 'completed',
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'created_at' => $now->copy(),
            ]);
        }

        $dara = Client::where('email', 'dara.meng@gmail.com')->first();
        if ($dara && Order::where('email', $dara->email)->count() === 0) {
            Order::create([
                'user_id' => 1,
                'email' => $dara->email,
                'phone' => $dara->phone,
                'shipping_address' => $dara->address,
                'subtotal' => 250.00,
                'tax' => 0,
                'shipping' => 3.00,
                'discount' => 10.00,
                'total' => 243.00,
                'status' => 'completed',
                'payment_method' => 'bakong_aba',
                'payment_status' => 'paid',
                'created_at' => $now->copy()->subDays(2),
            ]);
        }

        $vannak = Client::where('email', 'vannak.keo@gmail.com')->first();
        if ($vannak && Order::where('email', $vannak->email)->count() === 0) {
            Order::create([
                'user_id' => 1,
                'email' => $vannak->email,
                'phone' => $vannak->phone,
                'shipping_address' => $vannak->address,
                'subtotal' => 85.00,
                'tax' => 0,
                'shipping' => 1.50,
                'discount' => 0,
                'total' => 86.50,
                'status' => 'completed',
                'payment_method' => 'card',
                'payment_status' => 'paid',
                'created_at' => $now->copy()->subDays(10),
            ]);
        }
    }
}
