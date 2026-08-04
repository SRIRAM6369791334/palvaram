<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\BannerImage;
use App\Models\Category;
use App\Models\DeliveryPerson;
use App\Models\MilkOrder;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductRefund;
use App\Models\ProductSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller {
    /**
    * Create a new controller instance.
    *
    * @return void
    */

    public function __construct() {
        $this->middleware( 'auth' );
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */

    public function index( Request $request ) {
        if ( view()->exists( $request->path() ) ) {

            return view( $request->path() );
        }
        return abort( 404 );
    }

    public function root() {
        $countData = DB::select( DB::raw( '
                SELECT
                    (SELECT COUNT(*) FROM users) AS user_count,
                    (SELECT COUNT(*) FROM delivery_people) AS delivery_person_count,
                    (SELECT COUNT(*) FROM categories) AS category_count,
                    (SELECT COUNT(*) FROM sub_categories) AS subcategory_count,
                    (SELECT COUNT(*) FROM products) AS product_count,
                    (SELECT COUNT(*) FROM areas) AS area_count,
                    (SELECT COUNT(*) FROM milk_orders) AS milk_sub_count,
                    (SELECT COUNT(*) FROM product_orders) AS product_orders_count,
                    (SELECT COUNT(*) FROM web_images) AS banner_images_count,
                    (SELECT COUNT(*) FROM product_varient) AS product_varient_count
            ' ) );

        // $userCount = $countData[ 0 ]->user_count;
        $deliverPersonCount = $countData[ 0 ]->delivery_person_count;
        $categoryCount = $countData[ 0 ]->category_count;
        $subcategoryCount = $countData[ 0 ]->subcategory_count;
        $productCount = $countData[ 0 ]->product_varient_count;
        $areaCount = $countData[ 0 ]->area_count;
        $milkSubCount = $countData[ 0 ]->milk_sub_count;
        $prodcutOrdersCount = $countData[ 0 ]->product_orders_count;
        $bannerImagesCount = $countData[ 0 ]->banner_images_count;

        $userCount =   User::select( 'users.*', 'user_addresses.address_line_one' )
        ->Join( 'user_addresses', 'users.user_default_address_id', '=', 'user_addresses.id' )
        ->count();

        $milkOrders =  MilkOrder::query()->with( 'product', 'customer.area' )->where( 'payment_status', 1 )->orderByDesc( 'id' )->limit( '5' )->get();

        $productOrders =  ProductOrder::query()->with( 'product', 'orderAddress.area', 'customer.area' )->where( 'payment_status', 'paid' )->where( 'status', 'pending' )->orderByDesc( 'id' )->limit( '5' )->get();

        $billing = ProductOrder::query()->where( 'payment_status','paid' )->where( 'status', 'pending' )->count();
        $pending = ProductOrder::query()->where( 'payment_status','paid' )->where( 'status', 'processing' )->count();

        $dispatch = ProductOrder::query()->where( 'payment_status','paid' )->where( 'status', 'shipped' )->count();
        $delivery = ProductOrder::query()->where( 'payment_status', 'paid' )->where( 'status', 'out_for_delivery' )->count();
        $ordersum = ProductOrder::query()->where( 'payment_status', 'paid' )->count();
        $productOrdercompleteCount  = ProductOrder::query()->where( 'payment_status', 'paid' )->where( 'status', 'delivered' )->count();
        $totalAmount = ProductOrder::where( 'payment_status', 'paid' )->sum( 'total_amount' );
        $productrefund = ProductRefund::query()->where( 'refund_status', 0 )->count();
        $productreturn = ProductOrder::query()->where( 'status', 'return' )->count();

        return view( 'index', compact( 'userCount', 'deliverPersonCount', 'categoryCount', 'productCount', 'areaCount', 'milkSubCount', 'prodcutOrdersCount', 'bannerImagesCount', 'milkOrders', 'productOrders', 'billing', 'pending', 'dispatch', 'delivery', 'ordersum', 'totalAmount', 'productrefund', 'productOrdercompleteCount', 'productreturn', 'subcategoryCount' ) );

        // $userCount = User::count();
        // $deliverPersonCount = DeliveryPerson::count();
        // $categoryCount = Category::count();
        // $productCount = Product::count();
        // $areaCount = Area::count();
        // $milkSubCount = MilkOrder::count();
        // $prodcutOrdersCount = ProductOrder::count();
        // $bannerImagesCount = BannerImage::count();

        // return view( 'index', compact( 'userCount', 'deliverPersonCount', 'categoryCount', 'productCount', 'areaCount', 'milkSubCount', 'prodcutOrdersCount', 'bannerImagesCount' ) );
    }

    /*Language Translation*/

    public function lang( $locale ) {
        if ( $locale ) {
            App::setLocale( $locale );
            Session::put( 'lang', $locale );
            Session::save();
            return redirect()->back()->with( 'locale', $locale );
        } else {
            return redirect()->back();
        }
    }

    public function updateProfile( Request $request ) {
        $id = ucfirst( Auth::user()->id );
        $request->validate( [
            'name' => [ 'required', 'string', 'max:255' ],
            'email' => [ 'required', 'string', 'email' ],
            'avatar' => [ 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024' ],
        ] );

        $user = User::find( $id );
        $user->name = $request->get( 'name' );
        $user->email = $request->get( 'email' );

        if ( $request->file( 'avatar' ) ) {
            if ( @file_exists( public_path( Auth::user()->avatar ) ) ) {
                @unlink( public_path( Auth::user()->avatar ) );
            }

            $avatar = $request->file( 'avatar' );
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path( '/images/' );
            $avatar->move( $avatarPath, $avatarName );
            $user->avatar = '/images/' . $avatarName;
        }

        $user->update();
        if ( $user ) {
            Session::flash( 'message', 'User Details Updated successfully!' );
            Session::flash( 'alert-class', 'alert-success' );
        } else {
            Session::flash( 'message', 'Something went wrong!' );
            Session::flash( 'alert-class', 'alert-danger' );
        }
        return redirect()->back()->with( 'success', 'YOU HAVE SUCCESSFULLY UPDATED!' );
    }

    // public function updatePassword( Request $request, $id )
    // {
    //     $request->validate( [
    //         'current_password' => [ 'required', 'string' ],
    //         'password' => [ 'required', 'string', 'min:6', 'confirmed' ],
    // ] );

    //     if ( !( Hash::check( $request->get( 'current_password' ), Auth::user()->password ) ) ) {
    //         return response()->json( [
    //             'isSuccess' => false,
    //             'Message' => 'Your Current password does not matches with the password you provided. Please try again.'
    // ], 200 );
    // Status code
    //     } else {
    //         $user = User::find( $id );
    //         $user->password = Hash::make( $request->get( 'password' ) );
    //         $user->update();
    //         if ( $user ) {
    //             Session::flash( 'message', 'Password updated successfully!' );
    //             Session::flash( 'alert-class', 'alert-success' );
    //             return response()->json( [
    //                 'isSuccess' => true,
    //                 'Message' => 'Password updated successfully!'
    // ], 200 );
    // Status code here
    //         } else {
    //             Session::flash( 'message', 'Something went wrong!' );
    //             Session::flash( 'alert-class', 'alert-danger' );
    //             return response()->json( [
    //                 'isSuccess' => true,
    //                 'Message' => 'Something went wrong!'
    // ], 200 );
    // Status code here
    //         }
    //     }
    // }

}
