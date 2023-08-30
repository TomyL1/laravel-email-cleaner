<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyStatusColumnInProcessingStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE processing_statuses CHANGE status status ENUM('pending', 'processing', 'completed', 'error', 'edit_ready') DEFAULT 'edit_ready'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE processing_statuses CHANGE status status ENUM('pending', 'processing', 'completed', 'error') DEFAULT 'pending'");
    }
}
