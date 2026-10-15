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
        Schema::create('shop_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')
                ->constrained('electricity_shops')
                ->restrictOnDelete();

            $table->foreignId('customer_id')
                ->constrained('e_customers')
                ->restrictOnDelete();

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->boolean('is_current')->default(true);

            $table->text('remarks')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            

            $table->timestamps();
            
            $table->index(
                ['shop_id', 'is_current'],
                'shop_customers_current_index'
            );

            $table->index(
                ['customer_id', 'is_current'],
                'shop_customers_customer_current_index'
            );

            $table->index(['shop_id', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_customers');
    }
};
