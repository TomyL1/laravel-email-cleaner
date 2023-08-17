<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;  // Make sure you have a File model that corresponds to your files table.

class FileController extends Controller
{
    public function uploadView()
    {
        return view('upload');  // Returns the upload form view.
    }

    public function store(Request $request)
    {
        try {
            // Validate the uploaded file
            $validated = $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:2048',  // This is an example validation. Adjust as necessary.
            ]);

            // Store the uploaded file and get its path
            $path = $request->file('file')->store('uploads');

            // Compute the checksum. This is just an example using md5. You can use another method if you prefer.
            $checksum = md5_file($request->file('file')->getRealPath());

            // Store file details in the database
            File::create([
                'instance_name' => $request->input('instance_name'),  // Adjust this if you have another method for naming.
                'file_path' => $path,
                'size' => $request->file('file')->getSize(),
                'uploaded_at' => now(),
                'checksum' => $checksum,
            ]);

            return back()->with('success', 'File uploaded successfully!');  // Redirect back to the upload form with a success message.
        } catch (\Exception $e) {
            return back()->with('error', 'Error uploading file!');  // Redirect back to the upload form with an error message.
        }
    }
}
