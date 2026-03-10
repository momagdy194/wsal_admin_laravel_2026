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
        Schema::create('franchise_owner_needed_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('document_name_front')->nullable();
            $table->string('document_name_back')->nullable();
            $table->string('image_type')->nullable();
            $table->boolean('is_editable')->default(true);
            $table->string('doc_type')->default('image');
            $table->boolean('has_identify_number')->default(false);
            $table->string('identify_number_locale_key')->nullable();
            $table->boolean('has_expiry_date')->default(false);
            $table->boolean('is_required')->default(true);
            $table->boolean('active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchise_owner_needed_documents');
    }
};
