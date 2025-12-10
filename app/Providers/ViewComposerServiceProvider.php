<?php

namespace App\Providers;

use App\Models\CertificateRequest;
use App\Models\Purok;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Using a view composer to share data with specific views
        View::composer('layouts.app', function ($view) {
            // Initialize all variables with default values
            $pendingCounts = [
                'pendingPuroks' => 0,
                'pendingCertificates' => 0,
                'pendingPwdSupports' => 0,
                'pendingGovtAssistance' => 0,
            ];

            // Define the table names and their corresponding model classes
            $tables = [
                'puroks' => ['model' => Purok::class, 'column' => 'status'],
                'certificate_requests' => ['model' => CertificateRequest::class, 'column' => 'status'],
                'pwd_supports' => ['model' => 'App\\Models\\PwdSupport', 'column' => 'status', 'table' => 'pwd_supports'],
                'government_assistance' => ['model' => 'App\\Models\\GovernmentAssistance', 'column' => 'status'],
            ];

            foreach ($tables as $table => $config) {
                $varName = 'pending'.str_replace('_', '', ucwords($table, '_'));

                try {
                    // Use the table name from config or the key as table name
                    $tableName = $config['table'] ?? $table;

                    if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, $config['column'])) {
                        $modelClass = is_string($config['model']) ? $config['model'] : (is_object($config['model']) ? get_class($config['model']) : null);
                        if ($modelClass && class_exists($modelClass)) {
                            $model = app($modelClass);
                            $pendingCounts[$varName] = $model->where($config['column'], 'pending')->count();
                        } else {
                            $pendingCounts[$varName] = \DB::table($tableName)->where($config['column'], 'pending')->count();
                        }
                    }
                } catch (\Exception $e) {
                    // Log the error but don't break the application
                    \Log::warning("Error checking pending count for {$table}: ".$e->getMessage());

                    continue;
                }
            }

            // Share all variables with the view
            $view->with($pendingCounts);
        });
    }
}
