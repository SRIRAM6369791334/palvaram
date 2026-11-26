<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use App\Models\Cart;
use App\Models\Wishlist;

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
    public function boot()
    {
        if (config('app.env') === 'production' || request()->header('x-forwarded-proto') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();

        // Separate per-IP buckets for each sensitive form. (Plain `throttle:N,1`
        // shares ONE counter per IP across all routes, so a few wrong OTP tries
        // would also lock the user out of login.)
        \Illuminate\Support\Facades\RateLimiter::for('login', fn ($r) => \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($r->ip()));
        \Illuminate\Support\Facades\RateLimiter::for('register', fn ($r) => \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($r->ip()));
        \Illuminate\Support\Facades\RateLimiter::for('otp-send', fn ($r) => \Illuminate\Cache\RateLimiting\Limit::perMinute(3)->by($r->ip()));
        \Illuminate\Support\Facades\RateLimiter::for('otp-verify', fn ($r) => \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($r->ip()));
        \Illuminate\Support\Facades\RateLimiter::for('contact', fn ($r) => \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($r->ip()));

        View::composer('*', function ($view) {
            // '*' runs for every view and partial (header, footer, each include...).
            // Compute the cart/wishlist numbers once per request instead of each time.
            static $shared = null;
            if ($shared !== null) {
                $view->with($shared);
                return;
            }

            $cartCount    = 0;
            $wishlistCount = 0;
            $cartSubtotal  = 0;

            if (Auth::check()) {
                $cartItems = Cart::with(['variant', 'product'])
                    ->where('user_id', Auth::id())
                    ->whereHas('variant')
                    ->whereHas('product')
                    ->get();

                $cartCount = $cartItems
                    ->unique(fn($item) => $item->product_id . '-' . $item->product_varient_id)
                    ->count();

                $cartSubtotal = $cartItems->sum(
                    fn($item) => ($item->variant->offer_price ?? 0) * $item->product_quantity
                );

                $wishlistCount = Wishlist::where('user_id', Auth::id())->count();

            } else {
                
                $guestIds = array_values(array_unique(array_filter([
                    request()->cookie('guest_token'),
                    request()->ip(),
                ])));

                $cartItems = Cart::with(['variant', 'product'])
                    ->whereIn('ip_addresscart', $guestIds)
                    ->whereNull('user_id')
                    ->whereHas('variant')
                    ->whereHas('product')
                    ->get();

                $cartCount = $cartItems
                    ->unique(fn($item) => $item->product_id . '-' . $item->product_varient_id)
                    ->count();

                $cartSubtotal = $cartItems->sum(
                    fn($item) => ($item->variant->offer_price ?? 0) * $item->product_quantity
                );
            }

            $countryId = session('country_id') ?? (Auth::check() ? (Auth::user()->country_id ?? 243) : 243);
            $currencySymbol = ((int)$countryId === 244) ? '$' : '₹';
            $currencyCode   = ((int)$countryId === 244) ? 'MXN' : 'INR';

            $shared = [
                'cartCount'      => $cartCount,
                'wishlistCount'  => $wishlistCount,
                'cartSubtotal'   => $cartSubtotal,
                'currencySymbol' => $currencySymbol,
                'currencyCode'   => $currencyCode,
                'countryId'      => $countryId,
            ];
            $view->with($shared);
        });
    }
}
