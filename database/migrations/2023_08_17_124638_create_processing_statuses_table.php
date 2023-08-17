<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessingStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processing_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_id');
            $table->enum('status', ['pending', 'processing', 'completed', 'error'])->default('pending');
            $table->text('response')->nullable();
            $table->string('download_link')->nullable();
            $table->timestamps();

            $table->foreign('file_id')->references('id')->on('cl_upload_files');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('processing_statuses');
    }
}
