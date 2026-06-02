<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
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
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        $this->ensureStorageLink();
    }

    private function ensureStorageLink(): void
    {
        $publicStorage = public_path('storage');
        $storageTarget = storage_path('app/public');

        if (File::exists($publicStorage) && !is_link($publicStorage)) {
            $entries = array_diff(scandir($publicStorage), ['.', '..']);
            if (empty($entries)) {
                File::deleteDirectory($publicStorage);
            } else {
                return;
            }
        }

        if (!File::exists($publicStorage) && File::exists($storageTarget)) {
            try {
                app('files')->link($storageTarget, $publicStorage);
            } catch (\Throwable $e) {
                // Si no se puede crear el enlace simbólico, continuar sin interrumpir.
            }
        }
    }
}
