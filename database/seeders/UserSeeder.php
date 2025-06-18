<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'otp' => null,
            'otp_created_at' => null,
            'is_otp_verified' => true,
            'otp_expires_at' => null,
            'reset_password_token' => null,
            'reset_password_token_expire_at' => null,
            'delete_token' => null,
            'delete_token_expires_at' => null,
            'deleted_at' => null,
            'role' => 'admin',
            'status' => 'verified',
            'is_verified' => true,
            'phone' => '1234567890',
            'passport_number' => 'A12345678',
            'registration_code' => 'ADM-'.Str::random(8),
            'agree_terms' => true,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Business Owner 1
        DB::table('users')->insert([
            'name' => 'Business Owner 1',
            'email' => 'business1@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'otp' => null,
            'otp_created_at' => null,
            'is_otp_verified' => true,
            'otp_expires_at' => null,
            'reset_password_token' => null,
            'reset_password_token_expire_at' => null,
            'delete_token' => null,
            'delete_token_expires_at' => null,
            'deleted_at' => null,
            'role' => 'business_owner',
            'status' => 'verified',
            'is_verified' => true,
            'phone' => '2345678901',
            'passport_number' => 'B12345678',
            'registration_code' => 'BUS-'.Str::random(8),
            'agree_terms' => true,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Business Owner 2
        DB::table('users')->insert([
            'name' => 'Business Owner 2',
            'email' => 'business2@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'otp' => null,
            'otp_created_at' => null,
            'is_otp_verified' => true,
            'otp_expires_at' => null,
            'reset_password_token' => null,
            'reset_password_token_expire_at' => null,
            'delete_token' => null,
            'delete_token_expires_at' => null,
            'deleted_at' => null,
            'role' => 'business_owner',
            'status' => 'verified',
            'is_verified' => true,
            'phone' => '3456789012',
            'passport_number' => 'B23456789',
            'registration_code' => 'BUS-'.Str::random(8),
            'agree_terms' => true,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Security Guard 1
        DB::table('users')->insert([
            'name' => 'Security Guard 1',
            'email' => 'guard1@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'otp' => null,
            'otp_created_at' => null,
            'is_otp_verified' => true,
            'otp_expires_at' => null,
            'reset_password_token' => null,
            'reset_password_token_expire_at' => null,
            'delete_token' => null,
            'delete_token_expires_at' => null,
            'deleted_at' => null,
            'role' => 'security_guard',
            'status' => 'verified',
            'is_verified' => true,
            'phone' => '4567890123',
            'passport_number' => 'G12345678',
            'registration_code' => 'SEC-'.Str::random(8),
            'agree_terms' => true,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Security Guard 2
        DB::table('users')->insert([
            'name' => 'Security Guard 2',
            'email' => 'guard2@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'otp' => null,
            'otp_created_at' => null,
            'is_otp_verified' => true,
            'otp_expires_at' => null,
            'reset_password_token' => null,
            'reset_password_token_expire_at' => null,
            'delete_token' => null,
            'delete_token_expires_at' => null,
            'deleted_at' => null,
            'role' => 'security_guard',
            'status' => 'verified',
            'is_verified' => true,
            'phone' => '5678901234',
            'passport_number' => 'G23456789',
            'registration_code' => 'SEC-'.Str::random(8),
            'agree_terms' => true,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
