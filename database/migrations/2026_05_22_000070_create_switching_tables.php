<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('switching_workflows', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('ots_reference')->unique();
            $table->string('status')->default('draft')->index();
            $table->string('losing_provider')->nullable()->index();
            $table->string('gaining_provider')->nullable()->index();
            $table->date('requested_switch_date')->nullable()->index();
            $table->timestamp('completed_at')->nullable()->index();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('switching_workflows');
    }
};
