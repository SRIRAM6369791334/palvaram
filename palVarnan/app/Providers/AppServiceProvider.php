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

        View::composer('*', function ($view) {
            $cartCount    = 0;
            $wishlistCount = 0;
            $cartSubtotal  = 0;

            if (Auth::check()) {
                $cartItems = Cart::with('variant')
                    ->where('user_id', Auth::id())
                    ->whereHas('variant') 
                    ->get();

                $cartCount = $cartItems
                    ->unique(fn($item) => $item->product_id . '-' . $item->product_varient_id)
                    ->count();

                $cartSubtotal = $cartItems->sum(
                    fn($item) => ($item->variant->offer_price ?? 0) * $item->product_quantity
                );

                $wishlistCount = Wishlist::where('user_id', Auth::id())->count();

            } else {
                
                $guestToken = request()->cookie('guest_token') ?? request()->ip();

                $cartItems = Cart::with('variant')
                    ->where('ip_addresscart', $guestToken)
                    ->whereNull('user_id')
                    ->whereHas('variant') 
                    ->get();

                $cartCount = $cartItems
                    ->unique(fn($item) => $item->product_id . '-' . $item->product_varient_id)
                    ->count();

                $cartSubtotal = $cartItems->sum(
                    fn($item) => ($item->variant->offer_price ?? 0) * $item->product_quantity
                );
            }

            $view->with('cartCount', $cartCount);
            $view->with('wishlistCount', $wishlistCount);
            $view->with('cartSubtotal', $cartSubtotal);
        });
    }
}
