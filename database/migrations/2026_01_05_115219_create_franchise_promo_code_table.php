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
        Schema::create('franchise_promo_code', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_location_id');
            $table->string('transport_type')->nullable();
            $table->string('code');
            $table->integer('minimum_trip_amount')->default(0);
            $table->integer('maximum_discount_amount')->default(0);
            $table->integer('discount_percent')->default(0);
            $table->integer('total_uses')->default(0);
            $table->integer('uses_per_user')->default(0);
            $table->integer('cummulative_maximum_discount_amount')->default(0);
            $table->integer('available_balance')->default(0);
            $table->dateTime('from');
            $table->dateTime('to');
            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->foreign('service_location_id')
                    ->references('id')
                    ->on('service_locations')
                    ->onDelete('cascade');
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchise_promo_code');
    }
};
