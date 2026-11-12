<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('password');
            }
            if (Schema::hasColumn('users', 'profile_id')) {
                $table->foreignId('profile_id')->nullable()->change();
            }
            if (Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->nullable()->change();
            }
        });

        // Set role based on role_id for existing records
        if (Schema::hasColumn('users', 'role_id')) {
            DB::table('users')->where('role_id', 2)->update(['role' => 'admin']);
            DB::table('users')->where(function ($query) {
                $query->where('role_id', '!=', 2)->orWhereNull('role_id');
            })->update(['role' => 'user']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
