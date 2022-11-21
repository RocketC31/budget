<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('currencies')->insert([
            [
                'name' => 'Libyan Dinar',
                'symbol' => 'LYD'
            ], [
                'name' => 'Tunisian Dinar',
                'symbol' => 'TND'
            ], [
                'name' => 'Ghanaian Cedis',
                'symbol' => 'GHS'
            ], [
                'name' => 'Sudanese Pound',
                'symbol' => 'SDG'
            ], [
                'name' => 'Moroccan Dirham',
                'symbol' => 'MAD'
            ], [
                'name' => 'Botswana Pula',
                'symbol' => 'BWP'
            ], [
                'name' => 'South African Rand',
                'symbol' => 'ZAR'
            ], [
                'name' => 'Egyptian Pound',
                'symbol' => 'EGP'
            ], [
                'name' => 'Eritrean Nakfa',
                'symbol' => 'ERN'
            ], [
                'name' => 'Zambian Kwacha',
                'symbol' => 'ZMW'
            ], [
                'name' => 'Angolan Kwanza',
                'symbol' => 'AKZ'
            ]
        ]);
    }

    public function down()
    {
        //
    }
};
