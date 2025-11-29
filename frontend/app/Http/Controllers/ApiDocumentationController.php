<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiDocumentationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display API documentation page.
     */
    public function index()
    {
        $user = Auth::user();
        $apiKeys = $user->apiKeys()->where('is_active', true)->get();
        $baseUrl = config('app.url', 'http://localhost:8000');
        
        return view('api-documentation.index', compact('apiKeys', 'baseUrl'));
    }
}
