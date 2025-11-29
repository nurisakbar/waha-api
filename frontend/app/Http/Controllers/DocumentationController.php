<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    /**
     * Display public documentation page.
     */
    public function index()
    {
        $baseUrl = config('app.url', 'http://localhost:8000');
        
        return view('documentation.index', compact('baseUrl'));
    }
}

