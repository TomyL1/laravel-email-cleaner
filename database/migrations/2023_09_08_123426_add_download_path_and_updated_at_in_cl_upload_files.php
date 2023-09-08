<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDownloadPathAndUpdatedAtInClUploadFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cl_upload_files', function (Blueprint $table) {
            $table->string('download_file_path')->after('file_path')->nullable();
            $table->timestamp('updated_at')->after('uploaded_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cl_upload_files', function (Blueprint $table) {
            //
        });
    }
}
