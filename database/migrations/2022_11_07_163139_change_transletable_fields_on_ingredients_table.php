<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });

        Schema::table('ingredients', function (Blueprint $table) {
            $table->jsonb('name')->unique();
            $table->jsonb('description')->nullable();
            $table->index('name', 'ingredient_name_search_index', 'gin');
        });
    }

    public function down()
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });

        Schema::table('ingredients', function (Blueprint $table) {
            $table->string('name');
            $table->text('description');
        });
    }
};
