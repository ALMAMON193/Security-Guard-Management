<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name')->nullable(); // User's name
            $table->string('email')->unique(); // User's unique email
            $table->timestamp('email_verified_at')->nullable(); // Email verification timestamp
            $table->string('password'); // User's password
            $table->string('otp')->nullable(); // One-time password
            $table->timestamp('otp_created_at')->nullable(); // OTP creation timestamp
            $table->boolean('is_otp_verified')->default(false); // OTP verification status
            $table->timestamp('otp_expires_at')->nullable(); // OTP expiration timestamp
            $table->string('reset_password_token')->nullable(); // Token for password reset
            $table->timestamp('reset_password_token_expire_at')->nullable(); // Password reset token expiration
            $table->string('delete_token')->nullable(); // Token for deletion
            $table->timestamp('delete_token_expires_at')->nullable(); // Deletion token expiration
            $table->timestamp('deleted_at')->nullable(); // Soft delete timestamp
            $table->enum('role', ['admin', 'business_owner', 'security_guard']); // User's role
            $table->enum('status', ['verified', 'pending', 'rejected'])->default('pending'); // User's status
            $table->boolean('is_verified')->default(false); // Verification user email verified
            $table->string('phone'); // User's phone number
            $table->string('passport_number'); // User's passport number
            $table->string('registration_code'); // User's registration code
            $table->boolean('agree_terms')->default(false); // Terms agreement status
            $table->text('rejection_reason')->nullable();

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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
