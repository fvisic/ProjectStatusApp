<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('overall_health');
            $table->index('current_phase');
            $table->index('planned_go_live');
            $table->index('project_type');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['overall_health']);
            $table->dropIndex(['current_phase']);
            $table->dropIndex(['planned_go_live']);
            $table->dropIndex(['project_type']);
        });
    }
};
