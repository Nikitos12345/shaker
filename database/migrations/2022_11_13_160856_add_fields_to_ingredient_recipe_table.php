<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ingredient_recipe', function (Blueprint $table) {
            $table->boolean('optional')->default(false);
        });
    }

    public function down()
    {
        Schema::table('ingredient_recipe', function (Blueprint $table) {
            $table->dropColumn(['optional']);
        });
    }
};
