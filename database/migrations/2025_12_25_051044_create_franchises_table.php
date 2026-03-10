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
        if(!Schema::hasTable('franchises')){
            Schema::create('franchises', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->unsignedInteger('user_id');
                $table->string('email', 150)->nullable();
                $table->string('password')->nullable();
                $table->string('mobile', 14)->unique();
                $table->uuid('zone_id');
                $table->string('city', 50)->nullable();
                $table->boolean('active')->default(true);
                $table->boolean('approve')->default(false);
                $table->timestamps();
                $table->softDeletes();
                

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

                $table->foreign('zone_id')
                    ->references('id')
                    ->on('zones')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchises');
    }
};
