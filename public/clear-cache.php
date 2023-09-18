<?php

use Illuminate\Support\Facades\Artisan;

if (isset($_GET['token']) && $_GET['token'] === '1f4G7jK9lp3NoPQR56sTuvwxZ78mzy0c') {
    require '../vendor/autoload.php';

    $app = require_once '../bootstrap/app.php';

    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    $kernel->bootstrap();

    if (isset($_GET['clear']) && $_GET['clear'] === 'true') {
        try {

            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            echo 'All caches cleared!';
        } catch (Exception $e) {
            echo 'Could not clear cache. Error: ',  $e->getMessage(), "\n";
        }
    }

    if (isset($_GET['cache']) && $_GET['cache'] === 'true') {

        try {

            Artisan::call('view:cache');
            Artisan::call('route:cache');
            Artisan::call('config:cache');
            Artisan::call('view:cache');

            echo 'All caches cached!';
        } catch (Exception $e) {
            echo 'Could not cache. Error: ',  $e->getMessage(), "\n";
        }
    }
} else {
    echo 'Unauthorized access.';
}


