<?php

namespace App\Http\Controllers;

use App\Models\ReferralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    /**
     * Display referral information and referred users
     */
    public function index()
    {
        $user = Auth::user();
        $referrals = $user->referrals()->latest()->paginate(20);
        $referralCount = $user->referral_count;
        $settings = ReferralSetting::getActive();
        
        // Calculate total bonus received
        $totalTextQuotaBonus = $referralCount * ($settings->text_quota_bonus ?? 0);
        $totalMultimediaQuotaBonus = $referralCount * ($settings->multimedia_quota_bonus ?? 0);
        
        // Get referral link
        $referralLink = route('register', ['ref' => $user->referral_code]);
        
        return view('referral.index', compact(
            'user',
            'referrals',
            'referralCount',
            'settings',
            'totalTextQuotaBonus',
            'totalMultimediaQuotaBonus',
            'referralLink'
        ));
    }
}
