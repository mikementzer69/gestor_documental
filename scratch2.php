<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    \Illuminate\Support\Facades\Storage::disk('google')->makeDirectory('test_folder_123');
    echo 'SUCCESS';
} catch (\Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
}
