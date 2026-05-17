<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InstallerController extends Controller
{
    public function welcome()
    {
        if ($this->alreadyInstalled()) {
            return redirect()->route('home');
        }
        return view('install.welcome');
    }

    public function requirements()
    {
        if ($this->alreadyInstalled()) {
            return redirect()->route('home');
        }

        $requirements = $this->checkRequirements();
        $allPass = $requirements['php']['status']
            && !in_array(false, $requirements['extensions'], true)
            && !in_array(false, $requirements['permissions'], true);

        return view('install.requirements', compact('requirements', 'allPass'));
    }

    public function database()
    {
        if ($this->alreadyInstalled()) {
            return redirect()->route('home');
        }
        $dbConfig = session('install.db_config', []);
        return view('install.database', compact('dbConfig'));
    }

    public function testDatabase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_name' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fill in all required fields.',
            ]);
        }

        try {
            $pdo = new \PDO(
                "mysql:host={$request->db_host};port={$request->db_port}",
                $request->db_username,
                $request->db_password ?? '',
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_TIMEOUT => 5]
            );

            return response()->json([
                'success' => true,
                'message' => 'Connection successful! Database server is reachable.',
            ]);
        } catch (\PDOException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unexpected error: ' . $e->getMessage(),
            ]);
        }
    }

    public function saveDatabase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_name' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('install.database')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $pdo = new \PDO(
                "mysql:host={$request->db_host};port={$request->db_port}",
                $request->db_username,
                $request->db_password ?? '',
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_TIMEOUT => 5]
            );

            $pdo->exec(
                "CREATE DATABASE IF NOT EXISTS `{$request->db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );

            session([
                'install.db_config' => [
                    'host' => $request->db_host,
                    'port' => $request->db_port,
                    'name' => $request->db_name,
                    'username' => $request->db_username,
                    'password' => $request->db_password ?? '',
                ]
            ]);

            return redirect()->route('install.config');
        } catch (\PDOException $e) {
            return redirect()->route('install.database')
                ->withErrors(['connection' => 'Database connection failed: ' . $e->getMessage()])
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->route('install.database')
                ->withErrors(['connection' => 'Unexpected error: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function config()
    {
        if ($this->alreadyInstalled()) {
            return redirect()->route('home');
        }
        if (!session()->has('install.db_config')) {
            return redirect()->route('install.database');
        }

        $appConfig = session('install.app_config', []);
        return view('install.config', compact('appConfig'));
    }

    public function saveConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url|max:255',
            'app_env' => 'required|in:production,staging,local',
            'app_debug' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('install.config')
                ->withErrors($validator)
                ->withInput();
        }

        session([
            'install.app_config' => [
                'name' => $request->app_name,
                'url' => $request->app_url,
                'env' => $request->app_env,
                'debug' => $request->boolean('app_debug'),
            ]
        ]);

        return redirect()->route('install.admin');
    }

    public function admin()
    {
        if ($this->alreadyInstalled()) {
            return redirect()->route('home');
        }
        if (!session()->has('install.app_config')) {
            return redirect()->route('install.config');
        }

        return view('install.admin');
    }

    public function saveAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('install.admin')
                ->withErrors($validator)
                ->withInput();
        }

        session([
            'install.admin' => [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => $request->password,
            ]
        ]);

        return redirect()->route('install.install');
    }

    public function install()
    {
        if ($this->alreadyInstalled()) {
            return redirect()->route('home');
        }
        if (!session()->has('install.admin')) {
            return redirect()->route('install.admin');
        }

        if (session()->has('install.log')) {
            session()->forget('install.log');
        }

        return view('install.install');
    }

    public function process()
    {
        if ($this->alreadyInstalled()) {
            return response()->json([
                'done' => true,
                'redirect' => route('install.complete'),
            ]);
        }

        @ini_set('memory_limit', '512M');
        @ini_set('max_execution_time', 300);

        $this->applyRuntimeConfig();

        $log = session('install.log', []);

        $doneSteps = array_filter($log, fn($e) => ($e['status'] ?? '') === 'done');
        $executedSteps = array_map(fn($e) => $e['step'], $doneSteps);

        $steps = [
            ['step' => 'config', 'label' => 'Applying database configuration'],
            ['step' => 'migrate', 'label' => 'Running database migrations'],
            ['step' => 'seed', 'label' => 'Seeding default data and admin user'],
            ['step' => 'storage', 'label' => 'Creating storage symlink'],
            ['step' => 'finalize', 'label' => 'Finalizing installation'],
        ];

        foreach ($steps as $stepDef) {
            if (in_array($stepDef['step'], $executedSteps)) {
                continue;
            }

            $log[] = [
                'step' => $stepDef['step'],
                'label' => $stepDef['label'],
                'status' => 'running',
                'message' => '',
            ];
            session(['install.log' => $log]);
            session()->save();

            try {
                $output = '';

                switch ($stepDef['step']) {
                    case 'config':
                        $this->applyRuntimeConfig();
                        $output = 'Database configuration applied.';
                        break;

                    case 'migrate':
                        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
                        try {
                            Artisan::call('migrate:fresh', ['--force' => true, '--database' => 'mysql', '--seed' => false]);
                            $output = Artisan::output();
                        } catch (\Exception $e) {
                            if (\Illuminate\Support\Str::contains($e->getMessage(), 'errno: 150')) {
                                $output = 'Migration continued with foreign key warnings. Running remaining migrations...' . "\n";
                                try {
                                    Artisan::call('migrate', ['--force' => true, '--database' => 'mysql']);
                                    $output .= Artisan::output();
                                } catch (\Exception $e2) {
                                    $output .= 'Remaining migrations: ' . $e2->getMessage();
                                }
                            } else {
                                \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
                                throw $e;
                            }
                        }
                        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
                        break;

                    case 'seed':
                        $this->seedAdminUser();
                        $output = 'Admin user and default data seeded.';
                        break;

                    case 'storage':
                        if (is_link(public_path('storage'))) {
                            unlink(public_path('storage'));
                        } elseif (File::exists(public_path('storage'))) {
                            File::deleteDirectory(public_path('storage'));
                        }
                        Artisan::call('storage:link');
                        $output = Artisan::output();
                        break;

                    case 'finalize':
                        $this->createInstalledLock();
                        $this->writeEnvFile();
                        $output = '.env file written with new application key.' . "\n";
                        $output .= 'Config cache skipped (will load fresh on next request).' . "\n";
                        break;
                }

                $log[count($log) - 1]['status'] = 'done';
                $log[count($log) - 1]['message'] = trim($output);
                session(['install.log' => $log]);
                session()->save();

                $lastEntry = $log[count($log) - 1] ?? [];
                $allDone = ($lastEntry['step'] ?? '') === 'finalize' && ($lastEntry['status'] ?? '') === 'done';

                if ($allDone) {
                    return response()->json([
                        'done' => true,
                        'log' => $log,
                        'redirect' => route('install.complete'),
                    ]);
                }

                return response()->json(['done' => false, 'log' => $log]);
            } catch (\Exception $e) {
                $log[count($log) - 1]['status'] = 'error';
                $log[count($log) - 1]['message'] = $e->getMessage();
                session(['install.log' => $log]);
                session()->save();

                return response()->json([
                    'done' => false,
                    'log' => $log,
                    'error' => true,
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'done' => true,
            'log' => $log,
            'redirect' => route('install.complete'),
        ]);
    }

    public function retry()
    {
        $log = session('install.log', []);

        if (!empty($log)) {
            $lastIndex = count($log) - 1;
            if ($log[$lastIndex]['status'] === 'error') {
                array_pop($log);
                session(['install.log' => $log]);
                session()->save();
            }
        }

        return response()->json(['success' => true]);
    }

    public function complete()
    {
        if (!File::exists(storage_path('installed'))) {
            return redirect()->route('install.install');
        }

        $appConfig = session('install.app_config', [
            'name' => config('app.name', 'My Store'),
            'url' => config('app.url', url('/')),
        ]);
        $admin = session('install.admin', [
            'email' => 'admin@example.com',
        ]);

        session()->forget('install');

        return view('install.complete', compact('appConfig', 'admin'));
    }

    protected function alreadyInstalled(): bool
    {
        return File::exists(storage_path('installed'));
    }

    protected function checkRequirements(): array
    {
        $phpVersion = PHP_VERSION;
        $phpOk = version_compare($phpVersion, '8.2', '>=');

        $extensions = [
            'ctype' => extension_loaded('ctype'),
            'curl' => extension_loaded('curl'),
            'dom' => extension_loaded('dom'),
            'fileinfo' => extension_loaded('fileinfo'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'pdo' => extension_loaded('pdo'),
            'session' => extension_loaded('session'),
            'tokenizer' => extension_loaded('tokenizer'),
            'xml' => extension_loaded('xml'),
            'gd' => extension_loaded('gd'),
            'pdo_mysql' => extension_loaded('pdo_mysql'),
        ];

        $optional = [
            'bcmath' => extension_loaded('bcmath'),
        ];

        $permissions = [
            'storage' => is_writable(storage_path()),
            'storage/logs' => is_writable(storage_path('logs')),
            'storage/framework' => is_writable(storage_path('framework')),
            'storage/framework/sessions' => is_writable(storage_path('framework/sessions')),
            'storage/framework/views' => is_writable(storage_path('framework/views')),
            'storage/framework/cache' => is_writable(storage_path('framework/cache')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            'root_dir' => is_writable(base_path()),
        ];

        return [
            'php' => [
                'required' => '8.2',
                'current' => $phpVersion,
                'status' => $phpOk,
            ],
            'extensions' => $extensions,
            'optional' => $optional,
            'permissions' => $permissions,
        ];
    }

    protected function writeEnvFile(): void
    {
        $dbConfig = session('install.db_config');
        $appConfig = session('install.app_config');
        $appKey = config('app.key');

        $e = function ($v) {
            if (preg_match('/[#"\'\\s\\\\$]/', $v)) {
                return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $v) . '"';
            }
            return $v;
        };

        $content = "APP_NAME={$e($appConfig['name'])}
APP_ENV={$appConfig['env']}
APP_KEY={$appKey}
APP_DEBUG=" . ($appConfig['debug'] ? 'true' : 'false') . "
APP_TIMEZONE=UTC
APP_URL={$e($appConfig['url'])}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST={$e($dbConfig['host'])}
DB_PORT={$dbConfig['port']}
DB_DATABASE={$e($dbConfig['name'])}
DB_USERNAME={$e($dbConfig['username'])}
DB_PASSWORD={$e($dbConfig['password'])}

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=file
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS={$e('hello@example.com')}
MAIL_FROM_NAME={$e('${APP_NAME}')}

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME={$e('${APP_NAME}')}
VITE_APP_URL={$e('${APP_URL}')}
";

        File::put(base_path('.env'), $content);

        if (session()->has('install.db_config.password')) {
            session()->put('install.db_config.password', '[SECURED]');
        }
    }

    protected function seedAdminUser(): void
    {
        $admin = session('install.admin');

        $userClass = '\\App\\Models\\User';

        if (class_exists($userClass)) {
            $user = $userClass::where('email', $admin['email'])->first();

            if (!$user) {
                $userClass::create([
                    'name' => $admin['first_name'] . ' ' . $admin['last_name'],
                    'email' => $admin['email'],
                    'password' => Hash::make($admin['password']),
                    'email_verified_at' => now(),
                    'role' => 'admin',
                ]);
            }

            if ($user && method_exists($user, 'assignRole')) {
                try {
                    if (!$user->hasRole('super_admin')) {
                        $user->assignRole('super_admin');
                    }
                } catch (\Exception $e) {
                }
                try {
                    if (!$user->hasRole('admin')) {
                        $user->assignRole('admin');
                    }
                } catch (\Exception $e) {
                }
            }
        }

        if (class_exists('\\Database\\Seeders\\DatabaseSeeder')) {
            try {
                Artisan::call('db:seed', ['--force' => true]);
            } catch (\Exception $e) {
            }
        }
    }

    protected function applyRuntimeConfig(): void
    {
        $dbConfig = session('install.db_config');
        $appConfig = session('install.app_config');

        if ($dbConfig) {
            $driver = 'mysql';
            config([
                'database.default' => $driver,
                "database.connections.{$driver}.driver" => $driver,
                "database.connections.{$driver}.host" => $dbConfig['host'],
                "database.connections.{$driver}.port" => $dbConfig['port'],
                "database.connections.{$driver}.database" => $dbConfig['name'],
                "database.connections.{$driver}.username" => $dbConfig['username'],
                "database.connections.{$driver}.password" => $dbConfig['password'],
                "database.connections.{$driver}.charset" => 'utf8mb4',
                "database.connections.{$driver}.collation" => 'utf8mb4_unicode_ci',
                "database.connections.{$driver}.prefix" => '',
                "database.connections.{$driver}.prefix_indexes" => true,
                "database.connections.{$driver}.strict" => true,
                "database.connections.{$driver}.engine" => 'InnoDB',
            ]);
            try {
                \Illuminate\Support\Facades\DB::purge($driver);
                \Illuminate\Support\Facades\DB::reconnect($driver);
                \Illuminate\Support\Facades\DB::statement('SET default_storage_engine=InnoDB');
            } catch (\Exception $e) {
            }
        }

        if ($appConfig) {
            config(['app.name' => $appConfig['name']]);
            config(['app.url' => $appConfig['url']]);
            config(['app.env' => $appConfig['env']]);
            config(['app.debug' => $appConfig['debug']]);
        }
    }

    protected function createInstalledLock(): void
    {
        File::put(storage_path('installed'), 'Installed at: ' . now()->toIso8601String());
    }
}
