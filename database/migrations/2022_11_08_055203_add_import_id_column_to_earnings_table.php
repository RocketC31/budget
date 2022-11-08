<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImportIdColumnToEarningsTable extends Migration {
    public function up() {
        Schema::table('earnings', function (Blueprint $table) {
            $table->unsignedInteger('import_id')->nullable()->after('space_id');

            // FK
            $table->foreign('import_id')->references('id')->on('imports');
        });
    }

    public function down() {
        Schema::table('earnings', function (Blueprint $table) {
            // FK
            $table->dropForeign('earnings_import_id_foreign');

            $table->dropColumn('import_id');
        });
    }
}
