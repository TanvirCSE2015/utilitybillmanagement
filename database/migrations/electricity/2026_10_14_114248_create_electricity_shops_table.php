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
        Schema::create('electricity_shops', function (Blueprint $table) {
            $table->id();
             $table->foreignId('area_id')
                ->constrained('areas')
                ->restrictOnDelete();

            $table->foreignId('substation_id')->nullable()->constrained('substations')->nullOnDelete();

            $table->string('shop_no', 50);
            $table->string('shop_name', 150)->nullable();

            $table->string('shop_block', 50);
            $table->decimal('shop_area', 10, 2)->default(0);
            $table->decimal('central_ac_area',8,2)->default(0);
            $table->decimal('common_ac_area',8,2)->default(0);
            $table->decimal('water_area',8,2)->default(0);

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();


            $table->timestamps();

             $table->unique(
                ['area_id', 'shop_no'],
                'electric_shops_area_shop_unique'
            );

            $table->index(['area_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electricity_shops');
    }
};
