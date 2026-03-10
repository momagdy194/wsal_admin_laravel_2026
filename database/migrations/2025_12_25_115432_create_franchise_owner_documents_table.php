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
        Schema::create('franchise_owner_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_id');
            $table->unsignedInteger('document_id');
            $table->string('image');
            $table->string('back_image')->nullable();
            $table->string('identify_number')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->text('comment')->nullable();
            $table->integer('document_status')->default(2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('owner_id')
                    ->references('id')
                    ->on('franchises')
                    ->onDelete('cascade');

            $table->foreign('document_id')
                    ->references('id')
                    ->on('franchise_owner_needed_documents')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchise_owner_documents');
    }
};
