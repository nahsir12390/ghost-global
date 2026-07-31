<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$path = resource_path('views/livewire/home.blade.php');
$compiled = app('blade.compiler')->compileString(file_get_contents($path));
$file = storage_path('framework/views/home-debug.php');
file_put_contents($file, $compiled);
echo $file, PHP_EOL;
