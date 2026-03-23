<?php

namespace App\Http\Controllers;

class PagesController extends Controller
{
    public function cgu()
    {
        return view('pages.cgu');
    }

    public function confidentialite()
    {
        return view('pages.confidentialite');
    }

    public function quiSommesNous()
    {
        return view('pages.qui-sommes-nous');
    }

    public function contact()
    {
        return view('pages.contact');
    }
    public function sendContact(Request $request)
{
    $request->validate([
        'prenom'  => ['required', 'string'],
        'nom'     => ['required', 'string'],
        'email'   => ['required', 'email'],
        'message' => ['required', 'min:10'],
    ]);

    // TODO: envoyer un email avec Mail::to('support@biocolis.fr')
    
    return back()->with('contact_sent', true);
}
}
