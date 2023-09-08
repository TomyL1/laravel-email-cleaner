<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class ProcessFiles extends Command
{
    protected $signature = 'files:process';
    protected $description = 'Process files through DeBounce';

    public function handle() {
        // Check and update status of files already sent for processing
        $this->checkProcessedFilesStatus();

        // Check if any files are currently processing
        $processingCount = DB::table('processing_statuses')->where('status', 'processing')->count();
        if ($processingCount === 0) {
            // Send new pending files to DeBounce for processing
            $this->sendPendingFilesToDebounce();
        }
    }

    protected function checkProcessedFilesStatus()
    {
        // Here, fetch all files with 'processing' status and have a list_id
        $processingFiles = DB::table('processing_statuses')->where('status', 'processing')->whereNotNull('list_id')->get();

        foreach ($processingFiles as $file) {
            $response = $this->fetchStatusFromDebounce($file->list_id);
            $success = $response->success;
            $status = $response->debounce->status;

            if ($success === '1') {
                if ($status === 'completed') {
                    $downloadLink = $response->debounce->download_link;

                    $downloadFilePath = $this->downloadFile($downloadLink);

                    if ($downloadLink) {
                        DB::table('processing_statuses')->where('id', $file->id)->update([
                            'status' => 'completed',
                            'download_link' => $downloadLink,
                            'download_file_path' => $downloadFilePath,
                            'updated_at' => now()
                        ]);

                        Log::info("File processing completed" . ' - File_id:' . $file->list_id);
                        $this->info("File processing completed" . ' - File_id:' . $file->list_id);

                    } else {
                        Log::error("Download link missing" . ' - File_id:' . $file->list_id);
                        $this->error("Download link missing" . ' - File_id:' . $file->list_id);
                    }


                } elseif ($status === 'preparing') {
                    Log::info("File processing preparing" . ' - File_id:' . $file->list_id);
                    $this->info("File processing preparing" . ' - File_id:' . $file->list_id);

                } elseif ($status === 'processing') {
                    Log::info("File processing processing" . ' - File_id:' . $file->list_id);
                    $this->info("File processing processing" . ' - File_id:' . $file->list_id);

                } else {
                    Log::info("Unknown status" . ' - File_id:' . $file->list_id . ' - Response: ' . json_encode($response));
                    $this->info("Unknown status" . ' - File_id:' . $file->list_id . ' - Response: ' . json_encode($response));
                }

            } elseif ($success === '0') {

                if (isset($status->debounce->error)) {
                    $error = $status->debounce->error;

                    DB::table('processing_statuses')->where('id', $file->id)->update([
                        'status' => 'error',
                        'response' => $error
                    ]);
                    Log::error("Error fetching status from DeBounce: " . $error);
                    $this->error("Error fetching status from DeBounce: " . $error);
                } else {
                    $error = "Unknown error";

                    DB::table('processing_statuses')->where('id', $file->id)->update([
                        'status' => 'error',
                        'response' => $error
                    ]);
                    Log::error("Unknown error" . '- File_id:' . $file->list_id);
                    $this->error("Unknown error" . '- File_id:' . $file->list_id);
                }
            } else {
                Log::error("Unknown error - no response from server at all" . '- File_id:' . $file->list_id);
            }
        }
    }

    protected function downloadFile($downloadLink)
    {
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->get($downloadLink);
            $fileName = md5($downloadLink);

            $downloadedFilePath = 'downloads/' . $fileName  . '.csv';
            Storage::put($downloadedFilePath, $response->getBody());

            return $downloadedFilePath;

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // You can log the error or handle it as per your application's requirements
            Log::error("Error fetching status from DeBounce: " . $e->getMessage());
            $this->error("Error fetching status from DeBounce: " . $e->getMessage());

            return null;  // or you can return a default response structure indicating an error
        }
    }

    protected function fetchStatusFromDebounce($listId)
    {
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('GET', 'https://bulk.debounce.io/v1/status/', [
                'headers' => [
                    'accept' => 'application/json',
                ],
                'query' => [
                    'api' => env('DEBOUNCE_API_KEY'),
                    'list_id' => $listId
                ]
            ]);

            // Decode JSON response and return as an object
            return json_decode($response->getBody()->getContents());

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // You can log the error or handle it as per your application's requirements
            Log::error("Error fetching status from DeBounce: " . $e->getMessage());

            return null;  // or you can return a default response structure indicating an error
        }
    }

    protected function sendPendingFilesToDebounce() {
        $nextFile = DB::table('processing_statuses')->where('status', 'pending')->first();

        if (!$nextFile) {
            $this->info("No pending files to process. Exiting...");
            return;
        }

        $responseData = $this->sendToDebounce($nextFile->file_id);

        if ($responseData->success == "1" && isset($responseData->debounce->list_id)) {
            DB::table('processing_statuses')->where('id', $nextFile->id)->update([
                'status' => 'processing',
                'list_id' => $responseData->debounce->list_id
            ]);
            Log::info("File sent for processing. Will check status on next run.");
            $this->info("File sent for processing. Will check status on next run.");
        } else {
            if ($responseData->success == "0") {

                if (isset($responseData->debounce->error)) {
                    $error = $responseData->debounce->error;
                } else {
                    $error = "Unknown error";
                }

                DB::table('processing_statuses')->where('id', $nextFile->id)->update([
                    'status' => 'error',
                    'response' => $error
                ]);
                Log::error("Error sending file to DeBounce: " . $error);
                $this->error("Error sending file to DeBounce: " . $error);
            }
        }
    }

    protected function sendToDebounce($fileId)
    {
        $file = DB::table('cl_upload_files')->where('id', $fileId)->first();
        $fileUrl = "https://catalyst.sk/" . $file->file_path;

        Log::info('Sending file to DeBounce:', ['fileUrl' => $fileUrl]);

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('GET', 'https://bulk.debounce.io/v1/upload/', [
                'headers' => [
                    'accept' => 'application/json',
                ],
                'query' => [
                    'url' => $fileUrl,
                    'api' => env('DEBOUNCE_API_KEY')
                ],
            ]);

            return json_decode($response->getBody()->getContents());
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error("Error sending file to DeBounce: " . $e->getMessage());

            return (object) [
                'success' => '0',
                'debounce' => [
                    'error' => $e->getMessage()
                ]
            ];
        }
    }
}
