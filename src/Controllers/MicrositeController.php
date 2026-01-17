<?php

require_once __DIR__ . '/../Models/School.php';

class MicrositeController
{
    private $pdo;
    private $schoolModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->schoolModel = new School($pdo);
    }

    public function index($slug)
    {
        $school = $this->schoolModel->findBySlug($slug);

        if (!$school || empty($school['microsite_html'])) {
            // 404 Not Found
            http_response_code(404);
            $this->render404();
            return;
        }

        // Render the stored HTML
        echo $school['microsite_html'];
    }

    private function render404()
    {
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Page Not Found</title>
            <style>
                body { font-family: sans-serif; text-align: center; padding: 50px; background: #f8fafc; color: #334155; }
                h1 { font-size: 2rem; margin-bottom: 1rem; }
                p { font-size: 1rem; color: #64748b; }
                a { color: #0d9488; text-decoration: none; font-weight: bold; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>Halaman Tidak Ditemukan</h1>
                <p>Maaf, halaman sekolah yang Anda cari tidak dapat ditemukan atau belum dikonfigurasi.</p>
                <div style='margin-top: 2rem;'>
                    <a href='index.php'>&larr; Kembali ke Beranda</a>
                </div>
            </div>
        </body>
        </html>";
    }
}
