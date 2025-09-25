<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ifsnop\Mysqldump\Mysqldump;
use Illuminate\Support\Facades\Response;


class DatabaseBackupController extends Controller
{
    public function download()
    {
        $filename = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path("app/backups/{$filename}");

        // Ensure folder exists
        if (!file_exists(storage_path("app/backups"))) {
            mkdir(storage_path("app/backups"), 0755, true);
        }

        try {
            $dump = new Mysqldump(
                "mysql:host=" . env('DB_HOST') . ";dbname=" . env('DB_DATABASE'),
                env('DB_USERNAME'),
                env('DB_PASSWORD')
            );
            $dump->start($path);
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }

        return response()->download($path)->deleteFileAfterSend(true);
    }
}
