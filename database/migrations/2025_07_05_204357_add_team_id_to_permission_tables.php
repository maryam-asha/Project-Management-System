<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
        // Schema::table('roles', function (Blueprint $table) {
        //     $table->foreignId('team_id')
        //         ->nullable()
        //         ->constrained('teams')
        //         ->onDelete('cascade');

        //     $table->unique(['name', 'guard_name', 'team_id']);
        // });

        // Schema::table('model_has_roles', function (Blueprint $table) {
        //     $table->foreignId('team_id')
        //         ->nullable()
        //         ->constrained('teams')
        //         ->onDelete('cascade');

        //     $table->unique([
        //         'role_id',
        //         'model_id',
        //         'model_type',
        //         'team_id'
        //     ], 'model_has_roles_role_model_type_team_unique');
        // });
        // Schema::table('model_has_permissions', function (Blueprint $table) {
        //     $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('cascade');

        //     $table->unique([
        //         'permission_id',
        //         'model_id',
        //         'model_type',
        //         'team_id'
        //     ], 'model_has_permissions_permission_model_type_team_unique');
        // });
    // }

    /**
     * Reverse the migrations.
     */
    // public function down(): void
    // {
        // Schema::table('model_has_permissions', function (Blueprint $table) {
        //     $table->dropForeign(['team_id']);
        //     $table->dropUnique('model_has_permissions_permission_model_type_team_unique');
        //     $table->dropColumn('team_id');
        // });

        // Schema::table('model_has_roles', function (Blueprint $table) {
        //     $table->dropForeign(['team_id']);
        //     $table->dropUnique('model_has_roles_role_model_type_team_unique');
        //     $table->dropColumn('team_id');
        // });

        // Schema::table('roles', function (Blueprint $table) {
        //     $table->dropUnique(['name', 'guard_name', 'team_id']);
        //     $table->dropForeign(['team_id']);
        //     $table->dropColumn('team_id');
        // });
    // }
    public function up(): void
    {
        Schema::table('model_has_roles', function (Blueprint $table) {
            // Drop the existing primary key
            $table->dropPrimary('model_has_roles_role_model_type_primary');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            // Make team_id nullable
            $table->unsignedBigInteger('team_id')->nullable()->change();

            // Add a unique index instead of primary key
            $table->unique(['role_id', 'model_id', 'model_type', 'team_id'], 'model_has_roles_unique');
        });
    }

    public function down(): void
    {
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropUnique('model_has_roles_unique');
            $table->unsignedBigInteger('team_id')->nullable(false)->change();
            $table->primary(['role_id', 'model_id', 'model_type', 'team_id'], 'model_has_roles_role_model_type_primary');
        });
    }
    
};
