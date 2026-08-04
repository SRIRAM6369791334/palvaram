<?php

namespace App\Http\Controllers;

use App\Models\BannerImage;
use App\Models\WebImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BannerImagesController extends Controller {
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function index() {
        $bannerImages =  BannerImage::orderBy( 'id', 'asc' )->get();
        $webbannerImages = WebImage::orderBy( 'id', 'asc' )->get();
        return view( 'pages.banner_images', compact( 'bannerImages', 'webbannerImages' ) );
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function create() {
        //
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function store( Request $request ) {
        $request->validate( [
            'banner_image' => 'required|mimes:png,jpg,webp,jpeg'
        ] );

        if ( $request->hasFile( 'banner_image' ) ) {
            $bannerImage = $request->file( 'banner_image' );
            $path =  $bannerImage->store( 'banner_images', 'public' );
            BannerImage::create( [
                'banner_image' =>  $path,
            ] );

            $bannerImages =  BannerImage::orderBy( 'id', 'asc' )->get();
            $webbannerImages = WebImage::orderBy( 'id', 'asc' )->get();
            return response()->json( [
                'message' => 'Banner Image Added Successfully',
                'bannerImages' => $bannerImages
            ] );
        }
        return redirect( 'bannerImages' )->with( 'error', 'No Image found' );
    }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function show( $id ) {
        //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function edit( $id ) {
        //
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function update( Request $request, $id ) {

        $banner = BannerImage::findOrFail( $id );

        $request->validate( [
            'banner_image' => $request->hasFile( 'banner_image' ) ? 'required|mimes:png,jpg,webp,jpeg' : ''
        ] );
        if ( $request->hasFile( 'banner_image' ) ) {
            $bannerImage = $request->file( 'banner_image' );
            $path =  $bannerImage->store( 'banner_images', 'public' );
            File::delete( public_path( 'images/' ) . $banner->banner_image );
            $banner->update( [
                'banner_image' =>  $path,
            ] );

            $bannerImages =  BannerImage::orderBy( 'id', 'asc' )->get();
            $webbannerImages = WebImage::orderBy( 'id', 'asc' )->get();

            return response()->json( [
                'message' => 'Banner Image Added Successfully',
                'bannerImages' => $bannerImages
            ] );
        } else {
            $banner->update( [
                'banner_name' => $request->banner_name,
            ] );

            $bannerImages =  BannerImage::orderBy( 'id', 'asc' )->get();
            $webbannerImages = WebImage::orderBy( 'id', 'asc' )->get();

            return response()->json( [
                'message' => 'Category Added Successfully',
                'bannerImages' => $bannerImages
            ] );
        }
    }

   public function updateimage(Request $request, $id)
{
    $webbannerImages = WebImage::findOrFail($id);

    // If a new image is uploaded, validate it
    if ($request->hasFile('image')) {
        $request->validate([
            'image' => 'required|mimes:png,jpg,jpeg,webp|max:4096',
        ]);

        $webbannerImage = $request->file('image');

        // Store the new image
        $path = $webbannerImage->store('web_images', 'public');

        // Delete the old image from public/images if it exists
        $oldImagePath = public_path('images/') . $webbannerImages->image;
        if (File::exists($oldImagePath)) {
            File::delete($oldImagePath);
        }

        // Update with new image path
        $webbannerImages->update([
            'image' => $path,
        ]);

    } else {
        // No new file uploaded: retain existing image from hidden input
        $request->validate([
            'existing_image' => 'required|string',
        ]);

        $webbannerImages->update([
            'image' => $request->existing_image,
        ]);
    }

    // Fetch updated list
    $webbannerImagesAll = WebImage::orderBy('id', 'asc')->get();

    return response()->json([
        'message' => 'Banner Image Updated Successfully',
        'webbannerImages' => $webbannerImagesAll,
    ]);
}

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function destroy( $id ) {
        $banner = BannerImage::findOrFail( $id );

        if ( File::exists( public_path( 'images/' ) . $banner->banner_image ) ) {
            File::delete( public_path( 'images/' ) . $banner->banner_image );
            $banner->delete();

            $bannerImages =  BannerImage::orderBy( 'id', 'asc' )->get();

            return response()->json( [
                'message' => 'Banner Added Successfully',
                'bannerImages' => $bannerImages
            ] );
        }

        return redirect( 'bannerImages' )->with( 'error', 'Banner Image Deleted Failed' );
    }

    // web banner delete

    public function destroyweb( $id ) {
        $webbannerImages = WebImage::findOrFail( $id );

        if ( File::exists( public_path( 'images/' ) . $webbannerImages->image ) ) {
            File::delete( public_path( 'images/' ) . $webbannerImages->image );
            $webbannerImages->delete();

            $webbannerImages = WebImage::orderBy( 'id', 'asc' )->get();

            return response()->json( [
                'webbannerImages' => 'Banner Delete Successfully',
                'webbannerImages' => $webbannerImages
            ] );
        }

        return redirect( 'webbannerImages' )->with( 'error', 'Banner Image Deleted Failed' );
    }

    public function updateOrder( Request $request ) {
        $newOrder = $request->input( 'order' );
        foreach ( $newOrder as $position => $bannerId ) {
            BannerImage::where( 'id', $bannerId )->update( [ 'banner_position' => $position ] );
        }
        return response()->json( [ 'success' => true ] );
    }

    public function addbanner( Request $request ) {
        $request->validate( [
            'image' => 'required|mimes:png,jpg,webp,jpeg'
        ] );

        if ( $request->hasFile( 'image' ) ) {
            $bannerImage = $request->file( 'image' );
            $path =  $bannerImage->store( 'web_images', 'public' );
            WebImage::create( [
                'image' =>  $path,
            ] );

            $webbannerImages = WebImage::orderBy( 'id', 'asc' )->get();

            return response()->json( [
                'message' => 'Banner Image Added Successfully',
                'webbannerImages' => $webbannerImages
            ] );
        }
        return redirect( 'webbannerImages' )->with( 'error', 'No Image found' );
    }
}
