<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('mobile_number');

            // Adding the selected_address_id field
            $table->unsignedBigInteger('selected_address_id')->nullable();

            // Adding the foreign key constraint for selected_address_id
            $table->foreign('selected_address_id')->references('id')->on('addresses')->onDelete('set null');

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, drop the foreign key constraint
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['selected_address_id']);
        });

        // Then, drop the users table
        Schema::dropIfExists('users');
    }
};
