<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClUploadFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_upload_files', function (Blueprint $table) {
            $table->id();
            $table->string('instance_name');
            $table->string('file_path');
            $table->unsignedBigInteger('size');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->string('checksum');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_upload_files');
    }
}
