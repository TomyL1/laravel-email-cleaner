<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class ProcessFiles extends Command
{
    protected $signature = 'files:process';
    protected $description = 'Process files through DeBounce';

    public function handle()
    {
        $processingFile = DB::table('processing_statuses')->where('status', 'processing')->first();

        if ($processingFile) {
            $this->info("Another file is currently being processed. Exiting...");
            return;
        }

        $nextFile = DB::table('processing_statuses')->where('status', 'pending')->first();

        if (!$nextFile) {
            $this->info("No pending files to process. Exiting...");
            return;
        }

        DB::table('processing_statuses')->where('id', $nextFile->id)->update(['status' => 'processing']);

        $responseData = $this->sendToDebounce($nextFile->file_id);

        if ($responseData->success == "1") {
            DB::table('processing_statuses')->where('id', $nextFile->id)->update([
                'list_id' => $responseData->debounce->list_id
            ]);

            if ($responseData->debounce->status === "completed") {
                DB::table('processing_statuses')->where('id', $nextFile->id)->update([
                    'status' => 'completed',
                    'download_link' => $responseData->debounce->download_link
                ]);
            } else if ($responseData->debounce->status === "processing") {
                // You might want to introduce another mechanism to frequently check the status using the list_id until processing is complete
                $this->info("File is still being processed.");
            }
        } else if ($responseData->success == "0") {
            if (isset($responseData->debounce->error)) {
                DB::table('processing_statuses')->where('id', $nextFile->id)->update([
                    'status' => 'error',
                    'response' => $responseData->debounce->error
                ]);
            } else {
                DB::table('processing_statuses')->where('id', $nextFile->id)->update([
                    'status' => 'error',
                ]);
            }
        } else {
            // If there is no success key in the response, we handle it as an error for simplicity
            DB::table('processing_statuses')->where('id', $nextFile->id)->update([
                'status' => 'error'
            ]);
        }
    }

    protected function sendToDebounce($fileId)
    {
        $file = DB::table('cl_upload_files')->where('id', $fileId)->first();
        $fileUrl = "https://catalyst.sk/uploads/" . $file->file_path;

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('GET', 'https://bulk.debounce.io/v1/upload/', [
                'headers' => [
                    'accept' => 'application/json',
                ],
                'query' => [
                    'url' => $fileUrl,
                    'api' => '5dadcc1b65140',
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
