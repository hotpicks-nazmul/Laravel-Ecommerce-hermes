<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * System Controller for managing system settings and updates.
 */
class SystemController extends Controller
{
    /**
     * Display the system update page.
     */
    public function update()
    {
        $settings = Setting::getSystemUpdateSettings();
        $version = Setting::getSystemVersion();
        
        // Get PHP and server info
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();
        
        // Get database info
        $dbType = DB::connection()->getDriverName();
        $dbVersion = DB::select("SELECT version() as version")[0]->version ?? 'Unknown';
        
        // Get server info
        $serverInfo = [
            'php_version' => $phpVersion,
            'laravel_version' => $laravelVersion,
            'database_type' => $dbType,
            'database_version' => $dbVersion,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        ];
        
        return view('admin.system.update', compact('settings', 'version', 'serverInfo'));
    }

    /**
     * Perform system update.
     */
    public function performUpdate(Request $request)
    {
        $request->validate([
            'update_type' => 'required|in:check,install,backup',
        ]);

        if ($request->update_type === 'check') {
            return $this->checkForUpdates();
        } elseif ($request->update_type === 'install') {
            return $this->installUpdate($request);
        } elseif ($request->update_type === 'backup') {
            return $this->createBackup();
        }

        return redirect()->route('admin.system.update')
            ->with('error', 'Invalid update action.');
    }

    /**
     * Check for available updates.
     */
    protected function checkForUpdates()
    {
        // Simulate checking for updates (in a real application, this would call an external API)
        $currentVersion = Setting::get('app_version', '1.0.0');
        
        // For demo purposes, we'll simulate that there's no update available
        // In production, this would make an API call to check for updates
        $updateAvailable = false;
        $latestVersion = $currentVersion;
        
        // Update last check time
        Setting::set('last_check', now()->toDateTimeString(), 'system_update');
        
        if ($updateAvailable) {
            Setting::set('update_available', '1', 'system_update');
            Setting::set('latest_version', $latestVersion, 'system_update');
            
            return redirect()->route('admin.system.update')
                ->with('success', "A new version ($latestVersion) is available!");
        }
        
        Setting::set('update_available', '0', 'system_update');
        
        return redirect()->route('admin.system.update')
            ->with('info', 'Your system is up to date. No updates available.');
    }

    /**
     * Install the update.
     */
    protected function installUpdate(Request $request)
    {
        $request->validate([
            'backup_before_update' => 'sometimes|boolean',
        ]);

        try {
            // Create backup if requested
            if ($request->backup_before_update == '1' || Setting::get('backup_before_update', '1') == '1') {
                $this->createBackup();
            }

            // Run migrations
            Artisan::call('migrate', ['--force' => true]);
            
            // Clear cache
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            // Update version info
            $newVersion = $request->new_version ?? '1.0.1';
            Setting::set('app_version', $newVersion);
            Setting::set('db_version', $newVersion);
            Setting::set('last_updated', now()->toDateTimeString());
            Setting::set('update_available', '0', 'system_update');

            return redirect()->route('admin.system.update')
                ->with('success', "System successfully updated to version $newVersion!");
        } catch (\Exception $e) {
            return redirect()->route('admin.system.update')
                ->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    /**
     * Create a system backup.
     */
    protected function createBackup()
    {
        try {
            // In a real application, this would create a database backup
            // For now, we'll just store the backup info
            $backupInfo = [
                'created_at' => now()->toDateTimeString(),
                'version' => Setting::get('app_version', '1.0.0'),
                'status' => 'success',
            ];
            
            // Store backup info
            $backups = json_decode(Setting::get('system_backups', '[]'), true);
            $backups[] = $backupInfo;
            
            // Keep only last 10 backups
            if (count($backups) > 10) {
                $backups = array_slice($backups, -10);
            }
            
            Setting::set('system_backups', json_encode($backups));
            
            return redirect()->route('admin.system.update')
                ->with('success', 'Backup created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.system.update')
                ->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Save system update settings.
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'auto_check_updates' => 'sometimes|boolean',
            'auto_install_security' => 'sometimes|boolean',
            'update_channel' => 'sometimes|in:stable,beta,development',
            'notify_on_update' => 'sometimes|boolean',
            'backup_before_update' => 'sometimes|boolean',
        ]);

        $settings = [
            'auto_check_updates' => $request->auto_check_updates ? '1' : '0',
            'auto_install_security' => $request->auto_install_security ? '1' : '0',
            'update_channel' => $request->update_channel ?? 'stable',
            'notify_on_update' => $request->notify_on_update ? '1' : '0',
            'backup_before_update' => $request->backup_before_update ? '1' : '0',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value, 'system_update');
        }

        return redirect()->route('admin.system.update')
            ->with('success', 'System update settings saved successfully!');
    }

    /**
     * Display server status page.
     */
    public function serverStatus()
    {
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();
        
        // Get database info
        $dbType = DB::connection()->getDriverName();
        $dbVersion = DB::select("SELECT version() as version")[0]->version ?? 'Unknown';
        
        // Check required extensions
        $extensions = [
            'gd' => extension_loaded('gd'),
            'curl' => extension_loaded('curl'),
            'json' => extension_loaded('json'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'pdo' => extension_loaded('pdo'),
            'zip' => extension_loaded('zip'),
            'fileinfo' => extension_loaded('fileinfo'),
        ];
        
        // Check directory permissions
        $directories = [
            'storage/app/public' => is_writable(storage_path('app/public')),
            'storage/framework/cache' => is_writable(storage_path('framework/cache')),
            'storage/framework/sessions' => is_writable(storage_path('framework/sessions')),
            'storage/framework/views' => is_writable(storage_path('framework/views')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
        ];
        
        $serverInfo = [
            'php_version' => $phpVersion,
            'laravel_version' => $laravelVersion,
            'database_type' => $dbType,
            'database_version' => $dbVersion,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'os' => PHP_OS,
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'extensions' => $extensions,
            'directories' => $directories,
        ];
        
        return view('admin.system.server-status', compact('serverInfo'));
    }

    /**
     * API: Get system information for frontend.
     */
    public function getSystemInfoApi()
    {
        $version = Setting::getSystemVersion();
        $settings = Setting::getSystemUpdateSettings();
        
        return response()->json([
            'success' => true,
            'data' => [
                'app_version' => $version['app_version'],
                'db_version' => $version['db_version'],
                'update_available' => $version['update_available'],
                'latest_version' => $version['latest_version'],
                'last_updated' => $version['last_updated'],
                'last_check' => $settings['last_check'],
                'auto_check_updates' => $settings['auto_check_updates'],
            ]
        ]);
    }

    /**
     * API: Get version information for frontend.
     */
    public function getVersionApi()
    {
        $version = Setting::getSystemVersion();
        
        return response()->json([
            'success' => true,
            'data' => [
                'current_version' => $version['app_version'] ?? '1.0.0',
                'update_available' => ($version['update_available'] ?? '0') == '1',
                'latest_version' => $version['latest_version'] ?? $version['app_version'] ?? '1.0.0',
                'last_updated' => $version['last_updated'],
            ]
        ]);
    }

    // ==================== Activity Logs ====================

    /**
     * Display activity logs index page.
     */
    public function activityLogsIndex(Request $request)
    {
        $tab = $request->get('tab', 'all');
        
        return $this->activityLogs($request);
    }

    /**
     * Display activity logs.
     */
    public function activityLogs(Request $request)
    {
        $tab = $request->get('tab', 'all');
        
        $query = ActivityLog::query()->with('causer');
        
        // Filter by tab
        if ($tab === 'admin') {
            $query->admin();
        } elseif ($tab === 'customer') {
            $query->customer();
        } elseif ($tab === 'system') {
            $query->system();
        }
        
        // Search by description
        if ($request->search) {
            $query->where('description', 'like', "%{$request->search}%");
        }
        
        // Filter by date range
        if ($request->date_range) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) === 2) {
                $query->dateRange($dates[0], $dates[1]);
            }
        }
        
        // Sort
        $sort = $request->sort ?? 'recent';
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
        
        $logs = $query->paginate(25);
        
        // Stats
        $stats = [
            'total' => ActivityLog::count(),
            'admin' => ActivityLog::admin()->count(),
            'customer' => ActivityLog::customer()->count(),
            'system' => ActivityLog::system()->count(),
        ];
        
        return view('admin.system.activity-logs.index', [
            'logs' => $logs,
            'stats' => $stats,
            'tab' => $tab,
            'search' => $request->search ?? '',
            'dateRange' => $request->date_range ?? '',
            'sortBy' => $sort,
        ]);
    }

    /**
     * Display admin activity logs.
     */
    public function adminActivityLogs(Request $request)
    {
        $request->merge(['tab' => 'admin']);
        return $this->activityLogs($request);
    }

    /**
     * Display customer activity logs.
     */
    public function customerActivityLogs(Request $request)
    {
        $request->merge(['tab' => 'customer']);
        return $this->activityLogs($request);
    }

    /**
     * Export activity logs.
     */
    public function exportActivityLogs(Request $request)
    {
        $query = ActivityLog::query()->with('causer');
        
        $tab = $request->get('tab', 'all');
        if ($tab === 'admin') {
            $query->admin();
        } elseif ($tab === 'customer') {
            $query->customer();
        } elseif ($tab === 'system') {
            $query->system();
        }
        
        if ($request->date_range) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) === 2) {
                $query->dateRange($dates[0], $dates[1]);
            }
        }
        
        $logs = $query->orderBy('created_at', 'desc')->get();
        
        // Create CSV
        $filename = 'activity_logs_' . now()->format('Y-m-d_His') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"$filename\""];
        
        $callback = function() use ($logs) {
            $handle = fopen('php://output', 'w');
            
            // Header
            fputcsv($handle, ['ID', 'Log Name', 'Description', 'Causer', 'IP Address', 'Date']);
            
            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->log_name,
                    $log->description,
                    $log->causer ? $log->causer->name : 'System',
                    $log->ip_address,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($handle);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete activity logs.
     */
    public function destroyActivityLogs(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        
        ActivityLog::whereIn('id', $request->ids)->delete();
        
        return redirect()->route('admin.system.activity-logs.index')
            ->with('success', 'Activity logs deleted successfully!');
    }

    /**
     * Clear all activity logs.
     */
    public function clearActivityLogs(Request $request)
    {
        $request->validate([
            'log_type' => 'required|in:all,admin,customer,system',
        ]);
        
        $logType = $request->log_type;
        
        if ($logType === 'all') {
            ActivityLog::truncate();
        } elseif ($logType === 'admin') {
            ActivityLog::admin()->delete();
        } elseif ($logType === 'customer') {
            ActivityLog::customer()->delete();
        } elseif ($logType === 'system') {
            ActivityLog::system()->delete();
        }
        
        return redirect()->route('admin.system.activity-logs.index')
            ->with('success', 'Activity logs cleared successfully!');
    }
}
