<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('temp_id'); //Usefull for get id from original earning or spending for migration only
            $table->unsignedInteger('space_id');
            $table->unsignedInteger('import_id')->nullable();
            $table->integer('recurring_id')->unsigned()->nullable();
            $table->integer('tag_id')->unsigned()->nullable();
            $table->date('happened_on');
            $table->string('description');
            $table->integer('amount');
            $table->string('type');
            $table->timestamps();
            $table->softDeletes();

            //FK
            $table->foreign('import_id')->references('id')->on('imports');
            $table->foreign('space_id')->references('id')->on('spaces');
            $table->foreign('tag_id')->references('id')->on('tags');
            $table->foreign('recurring_id')->references('id')->on('recurrings');
        });

        //Insert earnings in transactions table
        $sql = "INSERT INTO transactions
                SELECT
                    NULL, `id`, `space_id`, `import_id`, `recurring_id`, NULL, `happened_on`, `description`,
                   `amount`, 'earning', `created_at`, `updated_at`, `deleted_at`
                FROM earnings";
        DB::statement($sql);

        //Insert spendings in transactions table
        $sql = "INSERT INTO transactions
                SELECT
                    NULL, `id`, `space_id`, `import_id`, `recurring_id`, `tag_id`, `happened_on`, `description`,
                   `amount`, 'spending', `created_at`, `updated_at`, `deleted_at`
                FROM spendings";
        DB::statement($sql);

        //Drop activities on earnings and spendings
        $this->deleteActivities("spending");
        $this->deleteActivities("earning");

        //Add activities from transactions
        $this->insertActivities("transactions", "transaction");

        //Attachements convert earning
        $this->convertAttachmentsUp("earning");
        //Attachements convert spending
        $this->convertAttachmentsUp("spending");

        //Drop attachments column
        Schema::dropColumns("attachments", ["transaction_type"]);

        //Drop tempId
        Schema::dropColumns("transactions", ["temp_id"]);

        //Drop earnings table
        Schema::dropIfExists('earnings');

        //Drop spendings table
        Schema::dropIfExists('spendings');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Recreate earnings table
        Schema::create('earnings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('temp_id'); //Usefull for get id from original earning or spending for migration only
            $table->unsignedInteger('space_id');
            $table->unsignedInteger('import_id')->nullable();
            $table->integer('recurring_id')->unsigned()->nullable();
            $table->date('happened_on');
            $table->string('description');
            $table->integer('amount');
            $table->timestamps();
            $table->softDeletes();

            //FK
            $table->foreign('import_id')->references('id')->on('imports');
            $table->foreign('space_id')->references('id')->on('spaces');
            $table->foreign('recurring_id')->references('id')->on('recurrings');
        });

        //Recreate spendings table
        Schema::create('spendings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('temp_id'); //Usefull for get id from original earning or spending for migration only
            $table->unsignedInteger('space_id');
            $table->unsignedInteger('import_id')->nullable();
            $table->integer('recurring_id')->unsigned()->nullable();
            $table->integer('tag_id')->unsigned()->nullable();
            $table->date('happened_on');
            $table->string('description');
            $table->integer('amount');
            $table->timestamps();
            $table->softDeletes();

            //FK
            $table->foreign('import_id')->references('id')->on('imports');
            $table->foreign('space_id')->references('id')->on('spaces');
            $table->foreign('tag_id')->references('id')->on('tags');
            $table->foreign('recurring_id')->references('id')->on('recurrings');
        });

        Schema::table('attachments', function (Blueprint $table) {
            $table->string('transaction_type')->after("id");
        });

        //Re inject data on earnings
        $sql = "INSERT INTO earnings
                SELECT
                    NULL, `id`, `space_id`, `import_id`, `recurring_id`, `happened_on`, `description`,
                   `amount`, `created_at`, `updated_at`, `deleted_at`
                FROM transactions
                WHERE `type` = 'earning'";
        DB::statement($sql);

        $sql = "INSERT INTO spendings
                SELECT
                    NULL, `id`, `space_id`, `import_id`, `recurring_id`, `tag_id`, `happened_on`, `description`,
                   `amount`, `created_at`, `updated_at`, `deleted_at`
                FROM transactions
                WHERE `type` = 'spending'";
        DB::statement($sql);

        //Attachements convert earning
        $this->convertAttachmentsDown("earning");
        //Attachements convert spending
        $this->convertAttachmentsDown("spending");

        //Drop temp_id
        Schema::dropColumns("earnings", ["temp_id"]);
        Schema::dropColumns("spendings", ["temp_id"]);

        //Drop activities on transactions
        $this->deleteActivities("transaction");

        //Add activities from earnings
        $this->insertActivities("earnings", "earning");

        //Add activities from spendings
        $this->insertActivities("spendings", "spending");

        Schema::dropIfExists('transactions');
    }

    private function insertActivities(string $tableName, string $entityType)
    {
        $sql = "INSERT INTO `activities`
                SELECT
                    NULL,
                    `space_id` as spaceId,
                    (
                        SELECT
                            `user_id`
                        FROM
                            `user_space`
                        WHERE
                            `space_id` = spaceId
                    ),
                    `id`,
                    '{$entityType}',
                    CASE
                        WHEN `updated_at` = `created_at` THEN 'transaction.created'
                        ELSE 'transaction.deleted'
                    END,
                    `created_at`,
                    `updated_at`
                FROM {$tableName}
                WHERE
                    `updated_at` = `created_at`
                    OR `deleted_at` IS NOT NULL";
        DB::statement($sql);
    }

    private function deleteActivities(string $entityType)
    {
        $sql = "DELETE FROM `activities` WHERE `entity_type` = '{$entityType}'";
        DB::statement($sql);
    }

    private function convertAttachmentsUp(string $typeTransaction)
    {
        $sql = "UPDATE `attachments` AS at
                SET
                    at.`transaction_id` = (
                    SELECT `id`
                    FROM
                        transactions as t
                    WHERE
                        `temp_id` = at.`transaction_id`
                        AND t.`type` = '{$typeTransaction}'
                )
                WHERE at.`transaction_id` IN(
                    SELECT `temp_id`
                    FROM
                        transactions as t
                    WHERE
                        `temp_id` = at.`transaction_id`
                        AND t.`type` = '{$typeTransaction}'
                ) AND at.`transaction_type` = '{$typeTransaction}'";
        DB::statement($sql);
    }

    private function convertAttachmentsDown(string $typeTransaction)
    {
        if ($typeTransaction === "earning") {
            $table = "earnings";
        } else {
            $table = "spendings";
        }
        $sql = "UPDATE `attachments` AS at
                SET at.`transaction_type` = '{$typeTransaction}',
                    at.`transaction_id` = (
                    SELECT `id`
                    FROM
                        {$table} as t
                    WHERE
                        `temp_id` = at.`transaction_id`
                )
                WHERE at.`transaction_id` IN(
                    SELECT `temp_id`
                    FROM
                        {$table} as t
                    WHERE
                        `temp_id` = at.`transaction_id`
                )";
        DB::statement($sql);
    }
};
