<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sp_1_trx')->insert([
            'id_trx' => 1,
            'id_user' => 1,
            'order_code' => 'PasarAja-aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            'order_pin' => '1234',
            'status' => 'Finished',
            'taken_date' => '2024-01-01',
            'expiration_time' => 0,
            'confirmed_by' => 0,
            'canceled_message' => '',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
