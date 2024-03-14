<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;


class ArtisanController extends Controller
{
    public function routeCacheClear()
    {
        $exitCode = Artisan::call('route:cache');
        if ($exitCode === 0) {
            return 'Route cache cleared successfully.';
        } else {
            return 'Failed to clear route cache.';
        }
    }
    public function cacheClear()
    {
        $exitCode = Artisan::call('cache:clear');
        if ($exitCode === 0) {
            return 'Cache cleared successfully.';
        } else {
            return 'Failed to clear cache.';
        }
    }
    public function configCache()
    {
        $exitCode = Artisan::call('config:cache');

        if ($exitCode === 0) {
            return 'Configuration cache cleared successfully.';
        } else {
            return 'Failed to clear configuration cache.';
        }
    }

    public function viewClear()
    {
        $exitCode = Artisan::call('view:clear');

        if ($exitCode === 0) {
            return 'View cache cleared successfully.';
        } else {
            return 'Failed to clear view cache.';
        }
    }

    public function migrate()
    {
        $exitCode = Artisan::call('migrate');

        if ($exitCode === 0) {
            return 'Migration completed successfully.';
        } else {
            return 'Failed to migrate database.';
        }
    }

    public function seed()
    {
        $exitCode = Artisan::call('db:seed');

        if ($exitCode === 0) {
            return 'Seeding completed successfully.';
        } else {
            return 'Failed to seed database.';
        }
    }

    public function optimize()
    {
        $exitCode = Artisan::call('optimize');

        if ($exitCode === 0) {
            return 'Application optimized successfully.';
        } else {
            return 'Failed to optimize the application.';
        }
    }

    public function scheduleRun()
    {
        $exitCode = Artisan::call('schedule:run');

        if ($exitCode === 0) {
            return 'Scheduled commands executed successfully.';
        } else {
            return 'Failed to run scheduled commands.';
        }
    }
    public function cors()
    {
        $exitCode = Artisan::call('vendor:publish', [
            '--provider' => "Fruitcake\Cors\CorsServiceProvider",
        ]);

        if ($exitCode === 0) {
            return 'CorsServiceProvider configuration published successfully.';
        } else {
            return 'Failed to publish CorsServiceProvider configuration.';
        }
    }
}
