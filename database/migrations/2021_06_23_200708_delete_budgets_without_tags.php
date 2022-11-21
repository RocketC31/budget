<?php

use App\Models\Budget;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach (Budget::all() as $budget) {
            $hasStrayTag = $budget->tag_id && !$budget->tag;

            if ($hasStrayTag) {
                $budget->delete();
            }
        }
    }

    public function down(): void
    {
        //
    }
};
