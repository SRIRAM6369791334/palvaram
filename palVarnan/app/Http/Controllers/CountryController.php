<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    
    public function index()
    {
        $countries = Country::orderBy('name')->get();
        return view('country-selection', compact('countries'));
    }

    public function store(Request $request)
    {
        $country = Country::whereRaw('LOWER(name) = ?', [strtolower($request->country)])->first();

        if (!$country) {
            return redirect('/select-country')->with('error', 'Invalid country selected.');
        }

        session(['country_id' => $country->id]);

        return redirect('/');
    }
}
