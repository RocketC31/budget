<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('space_id');
            $table->string('name')->nullable()->default(null);
            $table->string('logo')->nullable()->default(null);
            $table->string('requisition_id')->nullable()->default(null);
            $table->string('account_id')->nullable()->default(null);
            $table->string('link')->nullable()->default(null);
            $table->timestamps();
            $table->foreign('space_id')->references('id')->on('spaces');
        });

        Schema::table('spaces', function (Blueprint $table) {
            $table->boolean('sync_active')->default(false)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('spaces', function (Blueprint $table) {
            $table->dropColumn('sync_active');
        });

        Schema::drop("banks");
    }
};
