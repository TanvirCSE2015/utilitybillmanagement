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
        Schema::create('meter_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_id')
                ->constrained('meters')
                ->restrictOnDelete();
            $table->unsignedTinyInteger('reading_month');
            $table->unsignedSmallInteger('reading_year');
            $table->date('reading_date')->nullable();

            
            $table->decimal('reading_value', 12, 2)->nullable();

            $table->enum('reading_status', [
                        'available',
                        'unavailable',
                    ])->default('available');
            $table->enum('reading_type', [
                        'initial',
                        'regular',
                        'replacement',
                        'correction',
                    ])->default('regular');

            $table->text('remarks')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(
                ['meter_id', 'reading_year', 'reading_month'],
                'meter_readings_meter_month_year_index'
            );

            $table->index(
                ['reading_year', 'reading_month'],
                'meter_readings_year_month_index'
            );

            $table->index(
                ['meter_id', 'reading_date'],
                'meter_readings_meter_date_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meter_readings');
    }
};
