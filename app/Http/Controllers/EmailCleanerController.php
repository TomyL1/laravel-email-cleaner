<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class EmailCleanerController extends Controller
{
    public function bulkValidate() {
        $client = new Client();
        $version = 'v1';
        try {
            $response = $client->request('GET', 'https://bulk.debounce.io/'. $version .'/upload/', [
                'headers' => [
                    'accept' => 'application/json',
                ],
                'query' => [
                    'url' => '',
                    'api' => '5dadcc1b65140',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return view('bulk_validate', ['data' => $data]);
        } catch (ClientException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            $error = json_decode($responseBody, true);
            return view('bulk_validate', ['data' => $error]);
        }
    }
}
