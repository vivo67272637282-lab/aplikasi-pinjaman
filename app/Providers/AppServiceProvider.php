<?php

namespace App\Providers;

use App\Models\CompanySetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Di Vercel, /tmp adalah satu-satunya folder yang writable
        if (isset($_ENV['VERCEL']) || getenv('VERCEL')) {
            $tmpPath = '/tmp/laravel';
            foreach (['cache', 'views', 'sessions', 'testing', 'logs'] as $dir) {
                $path = $tmpPath . '/framework/' . $dir;
                if (!is_dir($path)) {
                    mkdir($path, 0755, true);
                }
            }
            if (!is_dir($tmpPath . '/logs')) {
                mkdir($tmpPath . '/logs', 0755, true);
            }
            app()->useStoragePath($tmpPath);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
        View::composer([
            'laporan.partials.company-header',
            'slip.partials.company-identity',
        ], function ($view) {
            $companySetting = auth()->check()
                ? CompanySetting::where('user_id', auth()->id())->first()
                : null;
            $companyLogo = null;

            if ($companySetting?->logo_path && Storage::disk('public')->exists($companySetting->logo_path)) {
                $mime = Storage::disk('public')->mimeType($companySetting->logo_path);
                if (in_array($mime, ['image/png', 'image/jpeg', 'image/gif'], true)) {
                    $companyLogo = 'data:' . $mime . ';base64,' . base64_encode(
                        Storage::disk('public')->get($companySetting->logo_path)
                    );
                }
            }

            $view->with([
                'companyName' => $companySetting?->company_name ?: config('company.name'),
                'companyAddress' => $companySetting?->company_address,
                'companyLogo' => $companyLogo,
            ]);
        });
    }
}
