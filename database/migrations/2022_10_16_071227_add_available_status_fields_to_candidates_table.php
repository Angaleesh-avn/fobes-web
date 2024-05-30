<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvailableStatusFieldsToCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->enum('status', ['available', 'not_available', 'available_in'])->default('available');
            $table->date('available_in')->nullable()->after('status');
            $table->string('notice_period', 25)->nullable()->after('available_in');
            $table->string('current_ctc', 25)->nullable()->after('whatsapp_number');
            $table->string('expected_ctc', 25)->nullable()->after('current_ctc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidates', function (Blueprint $table) {
            //
        });
    }
}
