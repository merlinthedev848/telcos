<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_threads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject');
            $table->string('status')->default('open')->index();
            $table->string('message_id')->nullable()->index();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamps();

            $table->index(['customer_id', 'message_id']);
            $table->index(['customer_id', 'last_message_at']);
        });

        Schema::create('email_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('email_thread_id')->constrained()->cascadeOnDelete();
            $table->string('direction')->index();
            $table->string('from')->index();
            $table->string('to')->index();
            $table->string('subject');
            $table->longText('body');
            $table->json('headers')->nullable();
            $table->timestamps();

            $table->index(['email_thread_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_messages');
        Schema::dropIfExists('email_threads');
    }
};
