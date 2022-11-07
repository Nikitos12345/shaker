<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->float('strength')->default(0)->after('description');
            $table->string('taste')->nullable()->after('strength');
            $table->dropColumn('is_alcoholic');
        });
    }

    public function down()
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->boolean('is_alcoholic')->default(true);
            $table->dropColumn(['strength', 'taste']);
        });
    }
};
