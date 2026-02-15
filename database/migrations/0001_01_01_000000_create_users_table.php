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
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->useCurrent();
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
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('usermeta', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 20);
            $table->string('firstName', 50);
            $table->string('lastName', 50);
            $table->integer('xp')->default(0);
            $table->integer('cash')->default(15);
            $table->integer('gold')->default(500);
            $table->integer('energyMax')->default(100);
            $table->integer('energy')->default(100);
            $table->text('seenFlags')->default('a:1:{s:13:"ftue_complete";b:0;}');
            $table->boolean('isNew')->default(true);
            $table->boolean('firstDay')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('playermeta', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 20);
            $table->string('meta_key', 255);
            $table->longText('meta_value');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('userworlds', function (Blueprint $table) {
            $classicSize = 12;

            $table->id();
            $table->string('uid', 20);
            $table->string('type', 20);
            $table->integer('sizeX')->default($classicSize);
            $table->integer('sizeY')->default($classicSize);
            $table->longText('objects');
            $table->longText('messageManager');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
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
