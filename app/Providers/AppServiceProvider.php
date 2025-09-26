<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;
use Illuminate\Support\Facades\View;

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
        View::composer('*', function ($view) {
            $notifications = DB::table('notification as pn')
                ->join('products as p', 'pn.product_id', '=', 'p.id')
                ->select(
                    'pn.id as notification_id',
                    'pn.created_at',
                    'p.id as product_id',
                    'p.name as product_name',
                    'p.barcode',
                    'p.product_image as product_image'
                )
                ->orderBy('pn.created_at', 'desc')
                ->limit(5)
                ->get();

            $totalNotifications = $notifications->count();

            $view->with([
                'headerNotifications' => $notifications,
                'headerTotalNotifications' => $totalNotifications,
            ]);
        });
    }
}
