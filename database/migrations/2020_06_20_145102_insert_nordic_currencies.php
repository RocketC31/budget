<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('currencies')->insert([
            [
                'name' => 'Danish Krone',
                'symbol' => 'DKK'
            ],
            [
                'name' => 'Icelandic Krona',
                'symbol' => 'ISK'
            ],
            [
                'name' => 'Norwegian Krone',
                'symbol' => 'NOK'
            ],
            [
                'name' => 'Swedish Krona',
                'symbol' => 'SEK'
            ]
        ]);
    }

    public function down(): void
    {
        //
    }
};
