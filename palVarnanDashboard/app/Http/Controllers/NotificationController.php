<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public $NotificationSuccessMessage = "Reviews Added Successfully";

    private function formatReviews()
    {
        return Review::with(['product.category'])->latest()->get()->map(function ($rev) {
            return [
                'id'         => $rev->id,
                'cate_id'    => $rev->product->category_id ?? null,
                'pro_id'     => $rev->prod_id,
                'cate_name'  => $rev->product->category->category_name ?? 'N/A',
                'pro_name'   => $rev->product->product_name ?? 'N/A',
                'title'      => $rev->name ?? 'Customer',
                'content'    => $rev->title ?? '',
                'review'     => $rev->review,
                'star'       => (int) $rev->ratings,
                'approval'   => (int) ($rev->approval ?? 0),
                'created_at' => $rev->created_at ? $rev->created_at->format('d M Y') : '',
            ];
        });
    }

    public function index()
    {
        $notififils = $this->formatReviews();
        $products = Product::with('category')->get();
        $categories = Category::all();

        return view("pages.notification", compact("notififils", "products", "categories"));
    }

    public function notifications(Request $request)
    {
        $validate = $request->validate([
            'notification_title'   => "required",
            'notification_content' => "nullable",
            'category_id'          => "required",
            'product_id'           => "required",
            'product_review'       => "required",
            'star_rating'          => "required",
        ]);

        Review::create([
            'name'     => $validate['notification_title'],
            'title'    => $validate['notification_content'],
            'prod_id'  => $validate['product_id'],
            'review'   => $validate['product_review'],
            'ratings'  => (float) $validate['star_rating'],
            'approval' => 1,
        ]);

        return response()->json([
            "message"    => "Review Added Successfully",
            "notififils" => $this->formatReviews()
        ]);
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $validated = $request->validate([
            'notification_title'   => "required",
            'notification_content' => "nullable",
            'categoryid'           => "required",
            'product_id'           => "required",
            'product_review'       => "required",
            'star_rating'          => "required",
        ]);

        $review->update([
            'name'     => $validated['notification_title'],
            'title'    => $validated['notification_content'],
            'prod_id'  => $validated['product_id'],
            'review'   => $validated['product_review'],
            'ratings'  => (float) $validated['star_rating'],
            'approval' => $request->has('approval') ? 1 : 0,
        ]);

        return response()->json([
            "message"    => "Review Updated Successfully",
            "notififils" => $this->formatReviews()
        ]);
    }

    public function approve($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['approval' => 1]);

        return response()->json([
            "success"    => true,
            "message"    => "Review Approved Successfully",
            "notififils" => $this->formatReviews()
        ]);
    }

    public function destroy($id)
    {
        $deleted = Review::where('id', $id)->delete();

        if ($deleted) {
            return response()->json([
                "message"      => "Review Deleted Successfully",
                "notification" => $this->formatReviews()
            ]);
        }

        return response()->json([
            "message" => "Review Not Found or Could Not Be Deleted",
        ], 404);
    }

    public function getProductsByCategory($id)
    {
        $products = Product::where('category_id', $id)->get(['id', 'product_name']);
        return response()->json($products);
    }
}
