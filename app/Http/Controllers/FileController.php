<?php

namespace App\Http\Controllers;

use App\Models\File;  // Make sure you have a File model that corresponds to your files table.

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;


class FileController extends Controller
{
    public function uploadView()
    {
        return view('upload');  // Returns the upload form view.
    }

    public function dashboard()
    {
        $files = DB::table('cl_upload_files')
            ->leftJoin('processing_statuses', 'cl_upload_files.id', '=', 'processing_statuses.file_id')
            ->orderBy('cl_upload_files.uploaded_at', 'desc')
            ->paginate(10);

        return view('dashboard', ['files' => $files]);
    }



    public function download($file) {
        $path = Storage::path("downloads/" . $file);
        Log::info('download path: ' . $path);

        if (!Storage::exists("downloads/" . $file)) {
            abort(404);
        }

        return response()->download($path);
    }

    public function store(Request $request)
    {
        try {
            // Validate the uploaded file
            $validated = $request->validate([
                'file' => 'required|file|mimes:txt,csv|max:2048',  // This is an example validation. Adjust as necessary.
            ]);

            // Store the uploaded file and get its path
            $hashedName = pathinfo($request->file('file')->hashName(), PATHINFO_FILENAME); // without extension
            $extension = $request->file('file')->getClientOriginalExtension();

            $path = $request->file('file')->storeAs('uploads', $hashedName . '.' . $extension);


            // Compute the checksum. This is just an example using md5. You can use another method if you prefer.
            $checksum = md5_file($request->file('file')->getRealPath());

            // Store file details in the database
            $file = File::create([
                'instance_name' => $request->input('instance_name'),
                'message' => $request->input('message'),
                'file_path' => $path,
                'size' => $request->file('file')->getSize(),
                'uploaded_at' => now(),
                'checksum' => $checksum,
            ]);
            Log::info('About to insert into processing_statuses. File ID: ' . $file->id);
            // Insert a new record into 'processing_statuses' table with a status of 'pending'.
            DB::table('processing_statuses')->insert([
                'file_id' => $file->id,
                'status' => 'edit_ready',
                'created_at' => now(),
            ]);
            Log::info('Inserted into processing_statuses');

            return back()->with('success', 'File uploaded successfully!');  // Redirect back to the upload form with a success message.
        } catch (\Exception $e) {
            Log::error('Error uploading file: ' . $e->getMessage()); // Log the actual error message
            return back()->with('error', 'Error uploading file!');  // Redirect back to the upload form with an error message.
        }
    }

    public function viewFile($file, Request $request)
    {
        $path = Storage::path("uploads/" . $file);
        if (!Storage::exists("uploads/" . $file)) {
            abort(404);
        }

        $encoding = $request->input('encoding', 'UTF-8'); // Default to UTF-8
        $content = file_get_contents($path);
        $error = null;
        try {
            if ($encoding !== 'UTF-8') {
                $content = iconv($encoding, 'UTF-8', $content);
            }
        } catch (Exception $e) {
            // Handle the exception as needed, maybe set content to a message or log the error
            Log::error('Error converting encoding: ' . $e->getMessage());
            $error = 'Error converting encoding: ' . $e->getMessage();
        }

        $rows = str_getcsv($content, "\n"); //parse the rows
        $separator = $request->input('separator', ','); // Default to comma
        $request->session()->put('separator', $separator);

        foreach($rows as &$row) {
            $row = str_getcsv($row, $separator);
        }

        // Helper function to check if all elements in an array are null
        $allNull = function($row) {
            return empty(array_filter($row, function($item) { return $item !== null; }));
        };

        // Filter out the rows where all elements are null
        $rows = array_filter($rows, function($row) use ($allNull) {
            return !$allNull($row);
        });

        return view('viewFile', ['rows' => $rows, 'file' => $file, 'encoding' => $encoding, 'error' => $error]);
    }

    public function saveFile($file, $encoding, Request $request)
    {
        $path = Storage::path("uploads/" . $file);

        if (!Storage::exists("uploads/" . $file)) {
            abort(404);
        }

        $content = file_get_contents($path);

        if ($encoding !== 'UTF-8' && in_array($encoding, mb_list_encodings())) {
            try {
                $content = iconv($encoding, 'UTF-8', $content);
            } catch (\Exception $e) {
                // Handle exception
                Log::error('Error converting encoding: ' . $e->getMessage());
            }
        }

        $separator = $request->input('separator', ','); // Default to comma
        $request->session()->put('separator', $separator); // Save to session

        $rows = str_getcsv($content, "\n"); // Parse the rows

        foreach ($rows as &$row) {
            $row = str_getcsv($row, $separator); // Parse each row using custom separator
        }

        // Now convert back to CSV format
        $newContent = '';
        foreach ($rows as $rowNew) {
            $newContent .= implode(',', $rowNew) . "\n";
        }

        // Save the new content back to the file
        file_put_contents($path, $newContent);

        return redirect()->route('view.file', ['file' => $file])->with('success', 'File saved successfully.');
    }


}



