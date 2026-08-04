<?php

namespace App\Http\Controllers;

use App\Models\OfferImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class OfferImageController extends Controller
{
    public function index()
    {
        $offerImages =  OfferImage::orderBy('offer_position')->get();
        return view("pages.offer_images", compact("offerImages"));
    }

    public function offerImagess(Request $request)
    {
        $request->validate([
            "offer_image" => "required|mimes:png,jpg,webp,jpeg"
        ]);


        if ($request->hasFile("offer_image")) {
            $offerImage = $request->file("offer_image");
            $path =  $offerImage->store("offer_images", "public");
            OfferImage::create([
                "offer_image" =>  $path,
            ]);

            $offerImages =  OfferImage::orderBy('offer_position')->get();

            return response()->json([
                "message" => "offer Image Added Successfully",
                "offerImages" => $offerImages
            ]);
        }
        return redirect("offerImages")->with("error", "No Image found");
    }

    // update
    public function update(Request $request, $id)
    {

        $offer = OfferImage::findOrFail($id);

        $request->validate([
            "offer_image" => $request->hasFile("offer_image") ? "required|mimes:png,jpg,webp,jpeg" : ""
        ]);
        if ($request->hasFile("offer_image")) {
            $offerImage = $request->file("offer_image");
            $path =  $offerImage->store("offer_images", "public");

            File::delete(public_path("images/") . $offer->offer_image);
            $offer->update([
                "offer_image" =>  $path,
            ]);

            $offerImages =  OfferImage::orderBy('offer_position')->get();

            return response()->json([
                "message" => "offer Image Added Successfully",
                "offerImages" => $offerImages
            ]);
        } else {
            $offer->update([
                "offer_name" => $request->offer_name,
            ]);

            $offerImages =  OfferImage::orderBy('offer_position')->get();

            return response()->json([
                "message" => "Offer Image Added Successfully",
                "offerImages" => $offerImages
            ]);
        }
    }

    public function destroy($id)
    {
        $offer = OfferImage::findOrFail($id);

        if (File::exists(public_path("images/") . $offer->offer_image)) {
            File::delete(public_path("images/") . $offer->offer_image);
            $offer->delete();

            $offerImages = OfferImage::orderBy('offer_position')->get();

            return response()->json([
                "message" => "offer Added Successfully",
                "offerImages" => $offerImages
            ]);
        }

        return redirect("offerImages")->with("error", "offer Image Deleted Failed");
    }

}
