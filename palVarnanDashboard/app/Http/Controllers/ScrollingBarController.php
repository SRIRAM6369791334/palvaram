<?php

namespace App\Http\Controllers;

use App\Models\ScrollingBar;
use Illuminate\Http\Request;

class ScrollingBarController extends Controller
{
   public function index()
{
    $bars = ScrollingBar::take(3)->get();
    return view('pages.scrollingbar', compact('bars'));
}

public function store(Request $request)
{
    $request->validate([
        'messages.*' => 'required|string|max:255',
    ]);

    ScrollingBar::truncate(); // Clear existing messages

    foreach ($request->messages as $msg) {
        ScrollingBar::create(['message' => $msg]);
    }

    return redirect()->back()->with('success', 'Scrolling messages updated!');
}
}
