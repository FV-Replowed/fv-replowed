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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 20);
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('useravatars', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 20);
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('usermeta', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 20);
            $table->string('firstName', 50);
            $table->string('lastName', 50);
            $table->integer('xp');
            $table->integer('cash');
            $table->integer('gold');
            $table->integer('energyMax');
            $table->integer('energy');
            $table->text('seenFlags');
            $table->boolean('isNew');
            $table->boolean('firstDay');
            $table->timestamps();
        });

        Schema::create('playermeta', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 20);
            $table->string('meta_key', 255);
            $table->longText('meta_value');
        });

        Schema::create('userworlds', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 20);
            $table->string('type', 20);
            $table->integer('sizeX');
            $table->integer('sizeY');
            $table->text('objects');
            $table->text('messageManager');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('useravatars');
        Schema::dropIfExists('usermeta');
        Schema::dropIfExists('playermeta');
        Schema::dropIfExists('userworlds');
    }
};
