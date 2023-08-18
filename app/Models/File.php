<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'cl_upload_files';
    protected $fillable = [
        'instance_name', 'file_path', 'size', 'uploaded_at', 'checksum'
    ];

    public $timestamps = false;
}

