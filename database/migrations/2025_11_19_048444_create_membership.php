<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['basic', 'premium', 'family'])->default('basic');
            $table->enum('status', ['active', 'expired', 'cancelled', 'pending'])->default('pending');
            $table->string('payment_reference')->nullable();
            $table->decimal('amount', 8, 2)->default(0);
            $table->string('currency', 3)->default('CZK');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('payment_submitted_at')->nullable();
            $table->timestamp('payment_verified_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('expires_at');
            $table->index('payment_reference');
        });

        // Modify existing users table to work with new system
        Schema::table('users', function (Blueprint $table) {
            // Keep payment_reference for backward compatibility during migration
            // We'll remove these later after data migration
            $table->string('legacy_payment_reference')->nullable()->after('payment_reference');
            $table->timestamp('legacy_payment_submitted_at')->nullable()->after('payment_submitted_at');
            $table->timestamp('legacy_payment_verified_at')->nullable()->after('payment_verified_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('memberships');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'legacy_payment_reference',
                'legacy_payment_submitted_at',
                'legacy_payment_verified_at'
            ]);
        });
    }
};
