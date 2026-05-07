<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Insert the three built-in types that match existing string values
        $types = [
            ['name' => 'New Implementation', 'color' => 'blue',   'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Migration',          'color' => 'green',  'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CR / Change',        'color' => 'yellow', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('project_types')->insert($types);

        $ids = DB::table('project_types')->orderBy('sort_order')->pluck('id', 'sort_order');
        // sort_order 1 → new, 2 → migration, 3 → cr
        [$newId, $migrationId, $crId] = [$ids[1], $ids[2], $ids[3]];

        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('project_type_id')->nullable()->constrained('project_types');
        });

        DB::table('projects')->where('project_type', 'new')->update(['project_type_id' => $newId]);
        DB::table('projects')->where('project_type', 'migration')->update(['project_type_id' => $migrationId]);
        DB::table('projects')->where('project_type', 'cr')->update(['project_type_id' => $crId]);

        // Change project_type column from ENUM to nullable string for history/audit trail
        Schema::table('projects', function (Blueprint $table) {
            $table->string('project_type')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['project_type_id']);
            $table->dropColumn('project_type_id');
            $table->enum('project_type', ['new', 'migration', 'cr'])->default('new')->change();
        });

        DB::table('project_types')->delete();
    }
};
