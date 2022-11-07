<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->jsonb('glass')->nullable();
            $table->jsonb('garnish')->nullable();
            $table->jsonb('category')->nullable();
            $table->jsonb('taste')->nullable();
            $table->renameColumn('details', 'preparation');
        });
    }

    public function down()
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn(['glass', 'garnish', 'category', 'taste']);
            $table->renameColumn('preparation', 'details');
        });
    }
};
