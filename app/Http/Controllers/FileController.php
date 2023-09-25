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

    public function deleteFile($file) {
        try {
            $fileDetails = DB::table('cl_upload_files')->where('id', $file)->first();
            if (!$fileDetails) {
                throw new Exception('File details not found.');
            }

            $filePath = 'uploads/'. $fileDetails->file_path;
            $downloadFilePath = 'downloads/'. $fileDetails->download_file_path;
            $originalFilePath = 'uploads/original/'. $fileDetails->file_path;

            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
            if (Storage::exists($downloadFilePath)) {
                Storage::delete($downloadFilePath);
            }
            if (Storage::exists($originalFilePath)) {
                Storage::delete($originalFilePath);
            }

            DB::table('processing_statuses')->where('file_id', $file)->delete();
            DB::table('cl_upload_files')->where('id', $file)->delete();

            return redirect()->route('dashboard')->with('success', 'File deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting file: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Error deleting file.');
        }
    }


    public function download($file) {
        $path = Storage::path("downloads/" . $file);
        Log::info('download path: ' . $path);

        if (!Storage::exists("downloads/" . $file)) {
            abort(404);
        }

        return response()->download($path);
    }

    public function downloadOriginal($fileId) {
        $filePath = DB::table('cl_upload_files')->where('id', $fileId)->first();

        $file = $filePath->file_path;

        if (!Storage::exists("uploads/original/" . $file)) {
            abort(404);
        }

        return response()->download(Storage::path("uploads/original/" . $file));
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


            $requestFile = $request->file('file');
            $path = $requestFile->storeAs('uploads', $hashedName . '.' . $extension);
            $requestFile->storeAs('uploads/original', $hashedName . '.' . $extension);


            // Compute the checksum. This is just an example using md5. You can use another method if you prefer.
            $checksum = md5_file($request->file('file')->getRealPath());

            // Store file details in the database
            $file = File::create([
                'instance_name' => $request->input('instance_name'),
                'message' => $request->input('message'),
                'file_path' => $hashedName . '.' . $extension,
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
        } catch (Exception $e) {
            Log::error('Error uploading file: ' . $e->getMessage()); // Log the actual error message
            return back()->with('error', 'Error uploading file!');  // Redirect back to the upload form with an error message.
        }
    }

    // FileController.php

    private function checkFileExists($fileId, $folder = false) {
        $file = DB::table('cl_upload_files')->where('id', $fileId)->first();

        if (!$folder) {
            $filePath = 'uploads/'. $file->file_path;
        } else if ($folder === 'downloads') {
            $filePath = 'downloads/'. $file->download_file_path;
        } else if ($folder === 'downloads/original') {
            $filePath = 'downloads/original/'. $file->download_file_path;
        } else {
            $filePath = 'uploads/'. $folder . '/' . $file->file_path;
        }
        if (!Storage::exists($filePath)) {
            abort(404);
        }
        return Storage::path($filePath);
    }
    private function checkFileStatus($fileId) {
        $file = DB::table('processing_statuses')->where('file_id', $fileId)->first();

        if (!$file) {
            return null;
        }
        return $file->status;
    }

    private function convertEncoding($content, $encoding) {
        if ($encoding !== 'UTF-8') {
            try {
                return iconv($encoding, 'UTF-8//IGNORE', $content);
            } catch (Exception $e) {
                Log::error('Error converting encoding: ' . $e->getMessage());
                throw $e;  // or return some default/fallback value
            }
        }
        return $content;
    }

    private function parseCsvContent($content, $separator) {
        $rows = str_getcsv($content, "\n");
        foreach ($rows as &$row) {
            $row = str_getcsv($row, $separator);
        }
        return $rows;
    }

    private function filterNullRows($rows) {
        return array_filter($rows, function($row) {
            return !empty(array_filter($row, function($item) { return $item !== null; }));
        });
    }

    public function viewFile($file, Request $request)
    {
        $status = $this->checkFileStatus($file);

        if ($status === 'completed') {
            $path = $this->checkFileExists($file, 'downloads');
        } else {
            $path = $this->checkFileExists($file);
        }
        $content = file_get_contents($path);
        $encoding = $request->input('encoding', 'UTF-8');
        $content = $this->convertEncoding($content, $encoding);
        $separator = $request->input('separator', ',');
        $request->session()->put('separator', $separator);
        $rows = $this->parseCsvContent($content, $separator);
        $rows = $this->filterNullRows($rows);

        return view('viewFile', ['rows' => $rows, 'file' => $file, 'fileStatus' => $status, 'encoding' => $encoding]);
    }

    public function saveFile($file, $encoding, Request $request)
    {
        $status = $this->checkFileStatus($file);

        if ($status === 'completed') {
            $path = $this->checkFileExists($file, 'downloads');
        } else {
            $path = $this->checkFileExists($file);
        }
        $content = file_get_contents($path);
        $content = $this->convertEncoding($content, $encoding);
        $separator = $request->input('separator', ',');
        $request->session()->put('separator', $separator);
        $rows = $this->parseCsvContent($content, $separator);
        $rows = $this->filterNullRows($rows);

        $selectedColumns = $request->input('columns', []);
        $deleteFirst = $request->has('deleteFirst');
        $clearDuplicates = $request->has('clearDuplicates');
        $reverseColumns = $request->has('reverseColumns');

        if ($deleteFirst) {
            array_shift($rows);
        }

        if (empty($selectedColumns)) {
            if (!$deleteFirst) {
                return redirect()->route('view.file', ['file' => $file])->with('error', 'You must select at least one column.');
            }
        } else {
            foreach ($rows as $index => $row) {
                $rows[$index] = array_intersect_key($row, array_flip($selectedColumns));
            }
        }
        $newContent = '';
        $uniqueEmails = [];
        foreach ($rows as $row) {
            if ($clearDuplicates) {
                $email = $row[0];

                try {
                    if ($email === '') {
                        throw new Exception('Email is empty.');
                    }
                } catch (Exception $e) {
                    Log::error('Error getting email: ' . $e->getMessage());
                    return redirect()->route('view.file', ['file' => $file])->with('error', 'Error getting email. The "Email" column must be the first column in the file.');
                }
                if (isset($uniqueEmails[$email])) {
                    continue;
                }
                $uniqueEmails[$email] = true;
            }

            if ($reverseColumns) {
                $row = array_reverse($row);
            }

            foreach ($row as &$item) {
                if (strpos($item, ',') !== false && !preg_match('/^".*"$/', $item)) {
                    $item = '"' . $item . '"';
                }
            }

            $newContent .= implode(',', $row) . "\n";
        }
        file_put_contents($path, $newContent);

        return redirect()->route('view.file', ['file' => $file])->with('success', 'File saved successfully.');
    }

    public function saveDeliverOnly($file, Request $request)
    {
        $status = $this->checkFileStatus($file);

        if ($status === 'completed') {
            $path =  $this->checkFileExists($file, 'downloads');
        }
        $content = file_get_contents($path);
        $content = $this->convertEncoding($content, 'UTF-8');
        $separator = ',';
        $index = $request->input('index', 0);
        $deliverText = $request->input('deliverText', 'Deliverable');

        $rows = $this->parseCsvContent($content, $separator);
        $rows = $this->filterNullRows($rows);

        $newContent = '';
        foreach ($rows as $row) {
            if ($row[$index] === $deliverText) {
                foreach ($row as &$item) {
                    if (strpos($item, ',') !== false && !preg_match('/^".*"$/', $item)) {
                        $item = '"' . $item . '"';
                    }
                }
                $newContent .= implode(',', $row) . "\n";
            }
        }
        file_put_contents($path, $newContent);

        return redirect()->route('view.file', ['file' => $file])->with('success', 'File saved successfully.');
    }

    public function finalizeFile($file, Request $request) {

        $update = DB::table('processing_statuses')
            ->where('file_id', $file)
            ->update(['status' => 'download_ready']);

        return redirect()->route('dashboard')->with('success', 'File finalized successfully.');
    }
    public function revertFile($file, Request $request)
    {
        try
        {
            $status = $this->checkFileStatus($file);

            if ($status === 'completed') {
                $folder = 'downloads/original';
            } else {
                $folder = 'original';
            }


            $path = $this->checkFileExists($file, $folder);

            $fileDetails = DB::table('cl_upload_files')->where('id', $file)->first();
            if (!$fileDetails) {
                throw new Exception('File details not found.');
            }

            if ($status === 'completed') {
                $fileName = $fileDetails->download_file_path;
                $destinationPath = Storage::path("downloads/" . $fileName);
            } else {
                $fileName = $fileDetails->file_path;
                $destinationPath = Storage::path("uploads/" . $fileName);
            }


            if (!copy($path, $destinationPath)) {
                throw new Exception('File revert failed.');
            }

            return redirect()->route('view.file', ['file' => $file])->with('success', 'File reverted successfully.');
        }
        catch (Exception $e)
        {
            Log::error('Error reverting file: ' . $e->getMessage());
            return redirect()->route('view.file', ['file' => $file])->with('error', 'Error reverting file.');
        }
    }


    public function submitToProcess($file, Request $request) {
        $result = DB::table('processing_statuses')
            ->where('file_id', $file)
            ->update(['status' => 'pending']);

        if (!$result) {
            return redirect()->route('dashboard')->with('error', 'Error updating status.');
        }
        return redirect()->route('dashboard')->with('success', 'File submitted for processing.');
    }
}
