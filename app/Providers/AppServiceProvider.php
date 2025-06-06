<?php

namespace App\Providers;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(\App\Providers\TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('viewPulse', function ($user = null) {
            return app()->environment('local') || optional($user)->email === 'admin@example.com';
        });

        // Laravel Safety Mechanisms
        $this->configureLaravelSafetyMechanisms();
    }

    /**
     * Configure Laravel safety mechanisms for better application security and performance.
     */
    private function configureLaravelSafetyMechanisms(): void
    {
        // 1. N+1 Prevention - Prevent lazy loading in non-production environments
        if (config('safety.prevent_lazy_loading.enabled', true)) {
            Model::preventLazyLoading(!$this->app->isProduction());

            // In production, log lazy loading violations instead of throwing exceptions
            if ($this->app->isProduction() && config('safety.prevent_lazy_loading.log_violations_in_production', true)) {
                Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                    $class = get_class($model);
                    Log::warning("Attempted to lazy load [{$relation}] on model [{$class}].");
                });
            }
        }

        // 2. Partially Hydrated Model Protection - Prevent accessing missing attributes
        if (config('safety.prevent_missing_attributes', true)) {
            Model::preventAccessingMissingAttributes(!$this->app->isProduction());
        }

        // 3. Model Strictness - Prevent silently discarding fillable attributes
        if (config('safety.prevent_silently_discarding_attributes', true)) {
            Model::preventSilentlyDiscardingAttributes(!$this->app->isProduction());
        }

        // 4. Polymorphic Mapping Enforcement - Enforce morph map to avoid storing FQCNs
        if (config('safety.enforce_morph_map', true)) {
            Relation::enforceMorphMap([
                // Add your polymorphic mappings here as needed
                // Example:
                // 'user' => \App\Models\User::class,
                // 'post' => \App\Models\Post::class,
            ]);
        }

        // 5. Long Database Query Monitoring
        if (config('safety.query_monitoring.enabled', true)) {
            $this->configureDatabaseQueryMonitoring();
        }

        // 6. Request and Command Lifecycle Monitoring
        if (config('safety.lifecycle_monitoring.enabled', true)) {
            $this->configureLifecycleMonitoring();
        }
    }

    /**
     * Configure database query monitoring for performance tracking.
     */
    private function configureDatabaseQueryMonitoring(): void
    {
        $cumulativeThreshold = config('safety.query_monitoring.cumulative_query_threshold', 2000);
        $individualThreshold = config('safety.query_monitoring.individual_query_threshold', 1000);

        // Log warning if cumulative query time exceeds threshold
        DB::whenQueryingForLongerThan($cumulativeThreshold, function (Connection $connection) use ($cumulativeThreshold) {
            Log::warning("Database queries exceeded {$cumulativeThreshold}ms on {$connection->getName()}");
        });

        // Log warning for individual slow queries
        DB::listen(function ($query) use ($individualThreshold) {
            if ($query->time > $individualThreshold) {
                Log::warning("An individual database query exceeded {$individualThreshold}ms.", [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time
                ]);
            }
        });
    }

    /**
     * Configure request and command lifecycle monitoring.
     */
    private function configureLifecycleMonitoring(): void
    {
        $commandThreshold = config('safety.lifecycle_monitoring.command_threshold', 5000);
        $requestThreshold = config('safety.lifecycle_monitoring.request_threshold', 5000);

        if ($this->app->runningInConsole()) {
            // Log slow commands
            $this->app[ConsoleKernel::class]->whenCommandLifecycleIsLongerThan(
                $commandThreshold,
                function ($startedAt, $input, $status) use ($commandThreshold) {
                    Log::warning("A command took longer than {$commandThreshold}ms.", [
                        'command' => $input->getArguments(),
                        'status' => $status,
                        'started_at' => $startedAt
                    ]);
                }
            );
        } else {
            // Log slow requests
            $this->app[HttpKernel::class]->whenRequestLifecycleIsLongerThan(
                $requestThreshold,
                function ($startedAt, $request, $response) use ($requestThreshold) {
                    Log::warning("A request took longer than {$requestThreshold}ms.", [
                        'url' => $request->fullUrl(),
                        'method' => $request->method(),
                        'status' => $response->getStatusCode(),
                        'started_at' => $startedAt
                    ]);
                }
            );
        }
    }
}
