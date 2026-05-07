<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // On production the column is still called user_id; on fresh installs
        // (tests) the create migration already uses created_by + updated_by.
        if (Schema::hasColumn('projects', 'user_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->renameColumn('user_id', 'created_by');
            });

            Schema::table('projects', function (Blueprint $table) {
                $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            });

            DB::statement('UPDATE projects SET updated_by = created_by');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('projects', 'updated_by') && Schema::hasColumn('projects', 'created_by')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropConstrainedForeignId('updated_by');
            });

            Schema::table('projects', function (Blueprint $table) {
                $table->renameColumn('created_by', 'user_id');
            });
        }
    }
};
