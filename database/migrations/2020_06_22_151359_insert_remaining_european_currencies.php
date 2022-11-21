<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('currencies')->insert([
            [
                'name' => 'Albanian Lek',
                'symbol' => 'ALL'
            ],
            [
                'name' => 'Armenian Dram',
                'symbol' => 'AMD'
            ],
            [
                'name' => 'Azerbaijani Manat',
                'symbol' => 'AZN'
            ],
            [
                'name' => 'Bosnia and Herzegovina Convertible Mark',
                'symbol' => 'BAM'
            ],
            [
                'name' => 'Bulgarian Lev',
                'symbol' => 'BGN'
            ],
            [
                'name' => 'Belarusian Ruble',
                'symbol' => 'BYN'
            ],
            [
                'name' => 'Swiss Franc',
                'symbol' => 'CHF'
            ],
            [
                'name' => 'Czech Koruna',
                'symbol' => 'CZK'
            ],
        ]);
    }

    public function down(): void
    {
        //
    }
};
