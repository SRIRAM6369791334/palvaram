<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of customer contact inquiries.
     */
    public function index()
    {
        $contacts = Contact::latest()->get();
        $totalCount = $contacts->count();
        $todayCount = Contact::whereDate('created_at', today())->count();
        $exportCount = Contact::where('subject', 'LIKE', '%Export%')->count();

        return view('pages.contacts', compact('contacts', 'totalCount', 'todayCount', 'exportCount'));
    }

    /**
     * Remove the specified contact inquiry from storage.
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Contact inquiry deleted successfully.');
    }
}
