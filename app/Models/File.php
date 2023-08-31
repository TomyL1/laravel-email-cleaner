<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'cl_upload_files';
    protected $fillable = [
        'instance_name', 'file_path', 'message', 'size', 'uploaded_at', 'checksum'
    ];

    public $timestamps = false;

    public function readCSVContent($lines = 100)
    {
        $filePath = storage_path('app/' . $this->file_path); // Change this to the correct path
        $file = fopen($filePath, 'r');
        $content = [];

        for ($i = 0; $i < $lines && !feof($file); $i++) {
            $content[] = fgetcsv($file);
        }
        fclose($file);

        return $content;
    }

}

