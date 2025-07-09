<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BackupDownloadRequest;
use App\Http\Requests\Admin\DeleteBackupRequest;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    public function index(BackupService $backupService)
    {
        $title = 'Бэкапы';
        $backups = $backupService->getBackups();

        return view('admin.backup.index', compact('title', 'backups'));
    }

    public function create(BackupService $backupService)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }
        $backupService->create();

        return redirect()->back()->with('success', "Файл бэкапа БД спешно создан!");
    }

    public function download(BackupDownloadRequest $request, BackupService $backupService)
    {
        $dir = $request->route('dir');
        $file = $request->route('file');

        return $backupService->download("{$dir}/{$file}");
    }

    public function delete(DeleteBackupRequest $request, BackupService $backupService)
    {
        $dir = $request->route('dir');
        $file = $request->route('file');

        $backupService->delete("{$dir}/{$file}");
        return redirect()->back()->with('success', "Файл бэкапа {$dir}/{$file} удалён успешно!");
    }
}
