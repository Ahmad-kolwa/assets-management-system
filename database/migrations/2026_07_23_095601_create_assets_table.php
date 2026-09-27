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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
                $table->foreignId('category_id')
          ->constrained()
          ->cascadeOnDelete();
 $table->string('asset_name');
    $table->string('asset_code')->unique();

    $table->enum('status', [
        'available',
        'assigned',
        'maintenance'
    ])->default('available');
$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
