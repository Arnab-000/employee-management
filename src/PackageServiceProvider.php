<?php

namespace Reddot\Employee\Management;

use Illuminate\Support\ServiceProvider;
use Reddot\Employee\Management\Console\Commands\ImportEmployeeHrInfo;
use Reddot\Employee\Management\Console\Commands\ImportUserData;
use ProcessMaker\Models\User as MainUser;
use Reddot\Employee\Management\Models\User as CustomUser;


class PackageServiceProvider extends ServiceProvider
{
    
    protected static $counter = 0;

    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'employee-management');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

    
        config()->set(
            'l5-swagger.documentations.default.paths.annotations',
            array_merge(
                config('l5-swagger.documentations.default.paths.annotations'),
                [base_path('packages/reddot/employee-management/src/Http/Controllers/Api')]
            )
        );
    }

    public function register()
    {
       
        
        self::$counter++;

        // Log the counter value
        // \Log::info("PackageServiceProvider called " . self::$counter . " times");

        // Get the call stack
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
        $caller = isset($backtrace[1]) ? $backtrace[1] : null;
        
        // Log the counter value and caller information
        \Log::info("PackageServiceProvider called " . self::$counter . " times by " . ($caller ? $caller['file'] . ' on line ' . $caller['line'] : 'unknown'));
        \Log::warning("package",$backtrace);
        
        
        // class_alias(CustomUser::class, MainUser::class);
        // if (class_exists(MainUser::class)) {
            class_alias(CustomUser::class, MainUser::class);
        // }
        
        $this->commands([
            ImportEmployeeHrInfo::class,
            ImportUserData::class,
        ]);
    }
}
