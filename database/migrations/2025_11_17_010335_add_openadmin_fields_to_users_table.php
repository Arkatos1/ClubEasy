<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, set unique usernames for all users
        \App\Models\User::all()->each(function ($user) {
            if (empty($user->username)) {
                $user->username = $user->email; // Use email as username
                $user->save();
            }
        });

        // Now add the unique constraint
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->string('username')->nullable()->change();
        });
    }
};
