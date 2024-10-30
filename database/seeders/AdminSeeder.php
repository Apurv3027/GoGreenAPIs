<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate users table
        User::truncate();
        DB::table('addresses')->truncate();

        // Admin User
        $admin = User::create([
            'fullname'          => config('const.admin.fullname'),
            'email'             => config('const.admin.email'),
            'email_verified_at' => Carbon::now(),
            'password'          => Hash::make(config('const.admin.password')),
            'mobile_number'     => config('const.admin.mobile_number'),
        ]);

        // Admin address
        $admin->addresses()->create([
            'street_1' => config('const.admin.street_1'),
            'street_2' => config('const.admin.street_2'),
            'city'     => config('const.admin.city'),
            'state'    => config('const.admin.state'),
        ]);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
