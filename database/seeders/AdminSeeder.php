<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::truncate();

        // Admin
        User::create([
            'fullname'        => config('const.admin.fullname'),
            'email'             => config('const.admin.email'),
            'email_verified_at' => Carbon::now(),
            'password'          => Hash::make(config('const.admin.password')),
            'mobile_number'     => config('const.admin.mobile_number'),
        ]);
    }
}
