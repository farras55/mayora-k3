<?php
// 1. Nyalakan sistem pelaporan error bawaan PHP secara paksa
error_reporting(E_ALL);
ini_set('display_errors', '1');

// 2. Trik folder storage sementara untuk Vercel
function createStorageDirs()
{
    $storageDirs = [
        '/tmp/storage/app/public',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/testing',
        '/tmp/storage/framework/views',
        '/tmp/storage/logs',
        '/tmp/bootstrap/cache',
    ];

    foreach ($storageDirs as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }
    
    $_ENV['APP_STORAGE'] = '/tmp/storage';
    putenv('APP_STORAGE=/tmp/storage');
}

// 3. Jalankan aplikasi di dalam pelindung "Try-Catch"
try {
    createStorageDirs();
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    // 4. Jika aplikasi crash, cetak detailnya ke layar putih!
    echo "<div style='font-family: sans-serif; padding: 20px;'>";
    echo "<h1>🚨 Error Fatal Tertangkap!</h1>";
    echo "<p><b>Pesan:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " di baris <b>" . $e->getLine() . "</b></p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto;'>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}