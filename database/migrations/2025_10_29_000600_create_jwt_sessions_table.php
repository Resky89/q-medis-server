<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jwt_sessions', function (Blueprint $table) {
            // Primary key named as in the screenshot
            $table->id('session_id');

            // Who owns the session
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->index();

            // Store the refresh token (recommended: store a hashed token)
            $table->text('refresh_token');

            // Expiration of the refresh token
            $table->timestampTz('expires_at');

            // Client context
            $table->text('user_agent')->nullable();
            $table->ipAddress('ip_address')->nullable();

            // Revocation timestamp
            $table->timestampTz('revoked_at')->nullable();

            $table->timestampsTz();

            // Indexes for lookups/cleanup
            $table->unique('refresh_token', 'ux_jwt_sessions_refresh_token');
            $table->index('expires_at', 'ix_jwt_sessions_expires_at');
            $table->index('revoked_at', 'ix_jwt_sessions_revoked_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jwt_sessions');
    }
};
