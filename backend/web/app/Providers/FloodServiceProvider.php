<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\River;

class FloodServiceProvider extends ServiceProvider
{
    // public function register()
    // {
    //     $this->app->singleton('flood-alert', function () {
    //         return new class {
    //             public function getApiUrl($riverId)
    //             {
    //                 $date = now()->subDay()->format('Ymd');
    //                 return "https://tethys.icimod.org/apps/flashfloodnp/chartHiwat/?stID={$riverId}&date={$date}";
    //             }
    //         };
    //     });
    // }
}