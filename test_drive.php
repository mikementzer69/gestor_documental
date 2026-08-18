<?php

require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$config = config('filesystems.disks.google');

$client = new \Google\Client();
$client->setClientId($config['clientId']);
$client->setClientSecret($config['clientSecret']);

try {
    $token = $client->fetchAccessTokenWithRefreshToken($config['refreshToken']);
    print_r($token);
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
