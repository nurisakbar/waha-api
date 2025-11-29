<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class LandingController extends Controller
{
    public function index()
    {
        // Jika user sudah login, redirect ke dashboard
        if (Auth::check()) {
            return redirect()->route('home');
        }
        
        // Jika belum login, tampilkan landing page
        $htmlPath = public_path('landing.html');
        
        if (File::exists($htmlPath)) {
            return response()->file($htmlPath);
        }
        
        // Fallback jika file tidak ada
        return response('Landing page not found', 404);
    }
}


