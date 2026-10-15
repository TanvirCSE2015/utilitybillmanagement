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
        Schema::create('meters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')
                ->constrained('electricity_shops')
                ->restrictOnDelete();

            $table->string('meter_no', 50)->unique();

            $table->date('installation_date');
            $table->date('removal_date')->nullable();

            $table->decimal('initial_reading', 12, 2)->default(0);

            $table->string('meter_status', 30)->default('active');

            $table->boolean('is_current')->default(true);

            $table->text('remarks')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            
            $table->timestamps();

            $table->index(
                ['shop_id', 'is_current'],
                'meters_shop_current_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meters');
    }
};
