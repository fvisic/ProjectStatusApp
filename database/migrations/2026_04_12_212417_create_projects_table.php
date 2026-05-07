<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('client')->nullable();
            $table->string('team_lead')->nullable();
            $table->date('report_date')->nullable();
            $table->date('project_start')->nullable();
            $table->date('planned_go_live')->nullable();
            $table->enum('project_type', ['new', 'migration', 'cr'])->default('new');
            $table->enum('current_phase', [
                'instalacija_analiza',
                'funkcionalna_specifikacija',
                'implementacija_testiranje',
                'integracije',
                'uat_edukacija',
                'go_live',
                'hypercare',
            ])->default('instalacija_analiza');
            $table->enum('overall_health', ['on_track', 'at_risk', 'off_track'])->default('on_track');
            $table->integer('estimated_hours')->nullable();
            $table->integer('spent_hours')->nullable();
            $table->integer('remaining_hours')->nullable();
            $table->text('estimation_comment')->nullable();
            $table->date('product_notification_deadline')->nullable();
            $table->string('product_notification_duration')->nullable();
            $table->text('product_notification_description')->nullable();
            $table->string('filled_by')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->string('version')->default('v1.0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
