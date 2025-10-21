<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Gate para verificar se é proprietária
        Gate::define('isProprietaria', function ($user) {
            return $user->tipo === 'proprietaria';
        });

        // Gate para verificar se é profissional
        Gate::define('isProfissional', function ($user) {
            return $user->tipo === 'profissional';
        });
    }
}
