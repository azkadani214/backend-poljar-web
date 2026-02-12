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
       Schema::create('memberships', function (Blueprint $table) {
    $table->uuid('id')->primary();

$table->uuid('user_id');
$table->foreign('user_id')
    ->references('id')
    ->on('users')
    ->cascadeOnDelete();


    $table->foreignUuid('division_id')
        ->constrained('divisions')
        ->cascadeOnDelete();

    $table->foreignUuid('position_id')
        ->constrained('positions')
        ->cascadeOnDelete();

    $table->boolean('is_active')->default(true);
    $table->string('period')->nullable(); // 2024/2025

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
