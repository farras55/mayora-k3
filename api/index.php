<?php
// 1. Buat direktori sementara di Vercel yang memiliki izin tulis (/tmp)
$tmpDirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/testing',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

// 2. Paksa Laravel agar log tidak ditulis ke file laravel.log, melainkan ke sistem Vercel
putenv('LOG_CHANNEL=stderr');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

try {
    // 3. Muat file pembangun inti Laravel secara manual
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';

    // 4. PENTING: Belokkan semua folder penyimpanan ke /tmp sebelum aplikasi menyala
    $app->useStoragePath('/tmp/storage');
    $app->useBootstrapPath('/tmp/bootstrap');

    // 5. Jalankan aplikasi (Otomatis mendeteksi Laravel versi 10 atau 11)
    if (method_exists($app, 'handleRequest')) {
        $app->handleRequest(Illuminate\Http\Request::capture());
    } else {
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        $response = $kernel->handle(
            $request = Illuminate\Http\Request::capture()
        )->send();
        $kernel->terminate($request, $response);
    }
} catch (\Throwable $e) {
    // Jika masih ada error sisa, tampilkan ke layar
    echo "<div style='font-family: sans-serif; padding: 20px;'>";
    echo "<h1>🚨 Error Tersisa:</h1>";
    echo "<p><b>Pesan:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " di baris <b>" . $e->getLine() . "</b></p>";
    echo "</div>";
}