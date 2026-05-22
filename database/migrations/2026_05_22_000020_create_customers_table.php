<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->string('account_number')->unique();
            $table->string('company_name')->nullable();
            $table->string('contact_name');
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->string('status')->default('lead')->index();
            $table->text('service_address')->nullable();
            $table->text('billing_address')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
