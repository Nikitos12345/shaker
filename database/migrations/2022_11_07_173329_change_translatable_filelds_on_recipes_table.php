<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'details']);
        });
        Schema::table('recipes', function (Blueprint $table) {
            $table->jsonb('name')->unique();
            $table->jsonb('description')->nullable();
            $table->jsonb('details')->nullable();
            $table->index('name', 'recipe_name_search_index', 'gin');
        });
    }

    public function down()
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'details']);
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('details')->nullable();
        });
    }
};
