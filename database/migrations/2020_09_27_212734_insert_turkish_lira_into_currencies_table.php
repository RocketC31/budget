<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('currencies')->insert([
            'name' => 'Turkish lira',
            'symbol' => 'TRY',
            'iso' => 'TRY'
        ]);
    }

    public function down(): void
    {
        //
    }
};
