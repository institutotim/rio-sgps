<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJustificationFieldsToFamilies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("families", function (Blueprint $table) {
            $table->boolean('is_justified')->index()->default(false)->after('visit_last');
            $table->string('justification')->nullable()->after('is_justified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
