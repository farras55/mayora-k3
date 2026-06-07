<?php

// Trik ini memaksa direktori storage dan cache Laravel
// agar tidak perlu izin menulis yang rumit di Vercel
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
    
    // Memberi tahu Laravel di mana lokasi storage dan cache yang baru
    $_ENV['APP_STORAGE'] = '/tmp/storage';
    putenv('APP_STORAGE=/tmp/storage');
}

// Jalankan fungsi pembuatan direktori sementara sebelum memuat aplikasi
createStorageDirs();

// Ini adalah baris asli yang kita butuhkan
require __DIR__ . '/../public/index.php';