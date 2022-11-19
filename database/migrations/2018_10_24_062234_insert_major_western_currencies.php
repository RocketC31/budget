<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('currencies')->insert([
            [
                'name' => 'Euro',
                'symbol' => '&euro;'
            ], [
                'name' => 'US Dollar',
                'symbol' => '&dollar;'
            ], [
                'name' => 'British Pound',
                'symbol' => '&pound;'
            ]
        ]);
    }

    public function down()
    {
        //
    }
};
