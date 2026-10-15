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
        Schema::create('e_customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_no', 30)->unique();
            $table->string('customer_name', 150);
            $table->string('mobile', 20)->nullable()->index();
            $table->string('nid', 30)->nullable()->index();
            $table->text('address')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_customers');
    }
};
