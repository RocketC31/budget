<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('currencies')->insert([
            [
                'name' => 'Bangladeshi taka',
                'symbol' => 'BDT',
                'iso' => 'BDT'
            ]
        ]);
    }

    public function down(): void
    {
        DB::table('currencies')
            ->where('iso', 'BDT')
            ->delete();
    }
};
