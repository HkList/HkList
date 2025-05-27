<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ResponseController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AutoUpdate
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config("database.default") === "no") return $next($request);

        $autoUpdatePath = app_path('Http/Middleware/AutoUpdate');
        $autoUpdatedPath = app_path('Http/Middleware/AutoUpdated');
        $debug = config('app.debug');

        if (!File::exists($autoUpdatedPath)) File::makeDirectory($autoUpdatedPath, 0755, true);
        $updateFiles = File::files($autoUpdatePath);

        foreach ($updateFiles as $file) {
            if ($file->getExtension() !== 'php') continue;

            try {
                require_once $file->getPathname();
                if (!$debug) {
                    $newPath = $autoUpdatedPath . '/' . $file->getFilename();
                    if (File::exists($file->getPathname())) File::move($file->getPathname(), $newPath);
                }
            } catch (Throwable $e) {
                if (!str_contains($e->getMessage(), "正在索引中")) Log::error("AutoUpdate Failed:" . $e);
                return ResponseController::autoUpdateError($e->getMessage());
            }
        }

        return $next($request);
    }
}
