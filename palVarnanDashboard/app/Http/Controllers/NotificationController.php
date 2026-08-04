<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Psy\Readline\Hoa\Console;

class NotificationController extends Controller
{

    public $NotificationSuccessMessage = "Reviews Added Successfully";
    public function index(){
        $notififils = Notification::all();
        $products =  Product::with('category')->get();
        $categories = Category::all();

        return view("pages.notification",compact("notififils","products", "categories"));


    }

    // add notifications

    // public function notifications(Request $request){

    //     $validate = $request->validate(
    //         [
    //             'notification_title' => "required",
    //             'notification_content' => "required",
    //             // 'notification_image' => "required|mimes:png,jpg,webp,jpeg",
    //             ]
    //         );


    //         if ($request->hasFile("notification_image")) {
    //             $productImage = $request->file("notification_image");
    //             $path =  $productImage->store("notification_image", "public");
    //             $notififils = AppNotification::create([...$validate, "notification_image" => $path]);

    //             $notififils = AppNotification::all();


    //             return response()->json([
    //                 "message" => $this->NotificationSuccessMessage,
    //                 "notififils" =>$notififils
    //             ]);
    //         }


    // }
    public function notifications(Request $request)
{
    $validate = $request->validate([
        'notification_title' => "required",
        'notification_content' => "nullable",
        'category_id' => "required",
        'product_id' => "required",
        'product_review' => "required",
        'star_rating' => "required",
    ]);

     $category = Category::find($validate['category_id']);
    $product = Product::find($validate['product_id']);

    $data = [
        'title' => $validate['notification_title'],
        'content' => $validate['notification_content'],
        'cate_id' => $validate['category_id'],
        'pro_id' => $validate['product_id'],
        'cate_name' => $category ? $category->category_name : null,
        'pro_name' => $product ? $product->product_name : null,
        'review' => $validate['product_review'],
        'star' => $validate['star_rating'],
        // 'approval' => $request->has('approval') ? 1 : 0,
        'approval' => 1,
    ];

    Notification::create($data); // Save only

     $notififils = Notification::all();

    return response()->json([
        "message" => $this->NotificationSuccessMessage,
        "notififils" =>$notififils
    ]);
}


     // Update coupons
    //  public function update(Request $request, $id)
    //  {
    //      $notification = AppNotification::findOrFail($id);

    //      $validated = $request->validate([
    //         'notification_title' => "required",
    //         'notification_content' => "required",
    //         "notification_image" => $request->hasFile("notification_image") ? "required|mimes:png,jpg,webp,jpeg" : "",

    //      ]);

    //      if ($request->hasFile("notification_image")) {


    //         $productImage = $request->file("notification_image");
    //         $path =  $productImage->store("notification_image", "public");
    //         File::delete(public_path("images/" . $notification->notification_image));
    //         $notification->update([
    //             ...$validated,
    //             "notification_image" =>  $path,
    //         ]);
    //         $notififils =  AppNotification::all();
    //         return response()->json([
    //             "message" => $this->NotificationSuccessMessage,
    //             "notififils" => $notififils
    //         ]);
    //     }else{



    //      $notification->update([
    //          'notification_title' => $validated["notification_title"],
    //          'notification_content' => $validated["notification_content"],



    //      ]);

    //      $notififils =  AppNotification::all();
    //      return response()->json([
    //          "message" => $this->NotificationSuccessMessage,
    //          "notififils" => $notififils
    //      ]);
    //     }
    //  }

    public function update(Request $request, $id)
{
    $notification = Notification::findOrFail($id);

    $validated = $request->validate([
        'notification_title' => "required",
        'notification_content' => "nullable",
         'categoryid' => "required",
    'product_id' => "required",
    'product_review' => "required",
    'star_rating' => "required",
    ]);

    $category = Category::find($validated['categoryid']);
$product = Product::find($validated['product_id']);


    $notification->update([
    'title' => $validated['notification_title'],
    'content' => $validated['notification_content'],
    'cate_id' => $validated['categoryid'],
    'pro_id' => $validated['product_id'],
    'cate_name' => $category->category_name ?? null,
    'pro_name' => $product->product_name ?? null,
    'review' => $validated['product_review'],
    'star' => $validated['star_rating'],
    'approval' => $request->has('approval') ? 1 : 0,
]);


    $notififils = Notification::all();

    return response()->json([
        "message" => $this->NotificationSuccessMessage,
        "notififils" => $notififils
    ]);
}


     public function destroy($id)
     {





         // Use a SQL DELETE query to remove the coupon with the given ID
         $notification = Notification::where('id', $id)->delete();

         if ($notification) {
             $notification = Notification::all();
             return response()->json([
                 "message" => "Reviews Deleted Successfully",
                 "notification" => $notification
             ]);
         } else {
             return response()->json([
                 "message" => "Reviews Not Found or Could Not Be Deleted",
             ], 404); // You can use a different HTTP status code if needed
         }
     }

public function getProductsByCategory($id)
{
    $products = Product::where('category_id', $id)->get(['id', 'product_name']);
    return response()->json($products);
}


}
