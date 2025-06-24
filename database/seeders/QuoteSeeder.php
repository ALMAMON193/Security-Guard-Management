<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('quotes')->insert([
            [
                'client_name' => 'Quote 1',
                'company_name' => 'Company 1',
                'client_contact' => 'John Doe',
                'client_email' => 'john@example.com',
                'area_of_operation' => 'security guards',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
