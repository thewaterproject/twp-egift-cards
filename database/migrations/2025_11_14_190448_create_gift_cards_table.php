<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('gift_card_id')->unique()->index();
            $table->string('status')->default('pending')->index();
            
            // Store nested objects as JSON
            $table->json('occasion')->nullable();
            $table->json('recipient')->nullable();
            $table->json('sender')->nullable();
            $table->json('delivery')->nullable();
            $table->json('gift')->nullable();
            $table->json('personalization')->nullable();
            $table->json('redemption')->nullable();
            $table->json('checkout')->nullable();
            
            // Store commonly queried fields as separate columns for performance
            $table->string('recipient_email')->nullable()->index();
            $table->string('sender_email')->nullable()->index();
            $table->decimal('amount', 10, 2)->nullable()->index();
            $table->string('currency', 3)->default('USD');
            $table->date('delivery_date')->nullable()->index();
            $table->boolean('send_immediately')->default(false);
            
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
