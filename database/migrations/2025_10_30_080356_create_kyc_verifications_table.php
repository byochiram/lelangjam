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
        Schema::create('kyc_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bidder_profile_id')->constrained()->cascadeOnDelete();
            $table->string('id_type')->default('KTP');
            $table->string('nik_hash');
            $table->string('nik_last4');
            $table->string('full_name');
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('ktp_image_url');
            $table->string('selfie_image_url');
            $table->string('status')->default('PENDING');
            $table->text('reason')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->index(['bidder_profile_id','status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_verifications');
    }
};
