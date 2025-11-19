<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'verified', 'cancelled'])->nullable()->after('deleted_at');
            $table->string('payment_reference', 20)->nullable()->after('payment_status');
            $table->timestamp('payment_submitted_at')->nullable()->after('payment_reference');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_submitted_at');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_reference',
                'payment_submitted_at',
                'payment_verified_at'
            ]);
        });
    }
};
