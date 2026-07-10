<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::define('manage-repository', function (\App\Models\User $user) {
            return $user->hasRole(['admin', 'super-admin']) || $user->id === 1;
        });

        // Status color blade directive
        Blade::directive('statuscolor', function ($expression) {
            return "<?php 
                \$colors = [
                    'en_proceso' => '#E9A15B',
                    'entregado' => '#B8DDBE', 
                    'aprobado' => '#5C9B68',
                    'atrasado' => '#D56E6E',
                    'rechazado' => '#A7A7AE',
                    'locked' => '#CBD5E1',
                    'active' => '#E9A15B',
                    'awaiting_decision' => '#B8DDBE',
                    'completed_viable' => '#5C9B68',
                    'completed_nonviable' => '#D56E6E',
                    'completed' => '#5C9B68',
                ];
                echo \$colors[\$expression] ?? '#CBD5E1';
            ?>";
        });
    }
}
