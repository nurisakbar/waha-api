<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $apiKeys = Auth::user()->apiKeys()->latest()->paginate(10);
        return view('api-keys.index', compact('apiKeys'));
    }

    public function create()
    {
        // Redirect to index with modal open
        return redirect()->route('api-keys.index');
    }

    public function show(ApiKey $apiKey)
    {
        if ($apiKey->user_id !== Auth::id()) abort(403);
        return redirect()->route('api-keys.index');
    }

    public function edit(ApiKey $apiKey)
    {
        if ($apiKey->user_id !== Auth::id()) abort(403);
        return redirect()->route('api-keys.index');
    }

    public function update(Request $request, ApiKey $apiKey)
    {
        if ($apiKey->user_id !== Auth::id()) abort(403);
        return redirect()->route('api-keys.index');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $key = 'waha_' . Str::random(48);
            $keyPrefix = substr($key, 0, 8);

            $apiKey = ApiKey::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'key' => hash('sha256', $key),
                'key_prefix' => $keyPrefix,
            ]);

            // Store the plain key in session temporarily to show to user
            // Use regular session (not flash) so it persists across page refreshes
            $userApiKeys = session('user_api_keys', []);
            $userApiKeys[$apiKey->id] = $key;
            session(['user_api_keys' => $userApiKeys]);
            
            // Also keep flash for backward compatibility
            session()->flash('api_key_plain', $key);
            session()->flash('api_key_id', $apiKey->id);

            return back()->with('success', 'API key created successfully! Copy and save your key below.');
        } catch (\Exception $e) {
            \Log::error('API Key creation failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['name' => 'Failed to create API key. Please try again.']);
        }
    }

    public function destroy(ApiKey $apiKey)
    {
        if ($apiKey->user_id !== Auth::id()) abort(403);
        $apiKey->delete();
        return back()->with('success', 'API key deleted successfully.');
    }
}
