<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReferralSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\EnsureUserIsAdmin::class);
    }

    /**
     * Display referral settings page
     */
    public function index()
    {
        $settings = ReferralSetting::getActive();
        return view('admin.referral-settings.index', compact('settings'));
    }

    /**
     * Update referral settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'text_quota_bonus' => 'required|integer|min:0',
            'multimedia_quota_bonus' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $settings = ReferralSetting::getActive();
        
        // If updating existing active settings, deactivate it and create new one
        if ($settings->is_active && $request->has('is_active') && $request->is_active) {
            $settings->update([
                'is_active' => false,
            ]);
            
            $settings = ReferralSetting::create([
                'text_quota_bonus' => $request->text_quota_bonus,
                'multimedia_quota_bonus' => $request->multimedia_quota_bonus,
                'is_active' => true,
            ]);
        } else {
            $settings->update($request->only([
                'text_quota_bonus',
                'multimedia_quota_bonus',
                'is_active',
            ]));
        }

        Log::info('Referral settings updated', [
            'admin_id' => auth()->id(),
            'settings' => $settings->toArray(),
        ]);

        return redirect()->route('admin.referral-settings.index')
            ->with('success', 'Referral settings berhasil diperbarui!');
    }
}
