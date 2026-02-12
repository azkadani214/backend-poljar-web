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
       Schema::create('divisions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');
    $table->timestamps();
});
Schema::create('positions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('division_id')
        ->constrained('divisions')
        ->cascadeOnDelete();

    $table->string('name'); // Ketua, Sekretaris, Staff
    $table->unsignedTinyInteger('level')->default(1);
    // makin tinggi = makin punya kuasa (opsional tapi kepake)

    $table->timestamps();
});


    }

    /**
     * Reverse the migrations.
     */
  public function down(): void
{
    Schema::dropIfExists('positions');
    Schema::dropIfExists('divisions');
}

};
