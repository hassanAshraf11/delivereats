<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuItemVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStateLog;
use App\Models\Rider;
use App\Models\Review;
use App\Models\PayoutSplit;
use App\Models\SurgePricingLog;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@delivereats.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
