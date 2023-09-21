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
        Schema::table('classrooms', function (Blueprint $table) {
            $table->softDeletes()->after('status'); // $table->timestamp('deleted_at')->nullable();
            $table->enum('status',  ['active', 'archived', 'deleted'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropSoftDeletes(); // $table->dropColumn('deleted_at');
            $table->enum('status',  ['active', 'archived'])->change();
        });
    }
};
