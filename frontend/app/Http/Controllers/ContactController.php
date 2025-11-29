<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppSession;
use App\Services\WahaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    protected WahaService $wahaService;

    public function __construct(WahaService $wahaService)
    {
        $this->middleware('auth');
        $this->wahaService = $wahaService;
    }

    public function index()
    {
        $sessions = Auth::user()->whatsappSessions()->where('status', 'connected')->get();
        return view('contacts.index', compact('sessions'));
    }

    public function show(WhatsAppSession $session)
    {
        if ($session->user_id !== Auth::id()) abort(403);
        
        $result = $this->wahaService->getContacts($session->session_id);
        $contacts = $result['success'] ? $result['data'] : [];
        
        return view('contacts.show', compact('session', 'contacts'));
    }

    public function groups()
    {
        $sessions = Auth::user()->whatsappSessions()->where('status', 'connected')->get();
        return view('groups.index', compact('sessions'));
    }

    public function groupShow(WhatsAppSession $session)
    {
        if ($session->user_id !== Auth::id()) abort(403);
        
        $result = $this->wahaService->getGroups($session->session_id);
        $groups = $result['success'] ? $result['data'] : [];
        
        return view('groups.show', compact('session', 'groups'));
    }
}
