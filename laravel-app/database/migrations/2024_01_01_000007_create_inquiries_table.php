<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('class');
            $table->string('phone')->nullable();
            $table->string('parent_phone');
            $table->string('source')->nullable(); // walk-in, referral, online, etc.
            $table->enum('status', ['new', 'follow-up', 'interested', 'not-interested', 'converted'])->default('new');
            $table->text('notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->foreignId('converted_student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
