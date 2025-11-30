<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ReferralSetting;
use App\Models\User;
use App\Models\UserQuota;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'captcha' => ['required', 'string'],
            'captcha_answer' => ['required', 'integer'],
            'referral_code' => ['nullable', 'string', 'exists:users,referral_code'],
        ], [
            'captcha.required' => 'Captcha harus diisi.',
            'captcha_answer.required' => 'Captcha answer harus ada.',
            'referral_code.exists' => 'Kode referral tidak valid.',
            'phone.required' => 'Nomor HP harus diisi.',
            'phone.unique' => 'Nomor HP sudah terdaftar.',
        ]);

        // Validate captcha
        $validator->after(function ($validator) use ($data) {
            $captcha = (int) ($data['captcha'] ?? 0);
            $captchaAnswer = (int) ($data['captcha_answer'] ?? 0);

            if ($captcha !== $captchaAnswer) {
                $validator->errors()->add('captcha', 'Captcha tidak benar. Silakan coba lagi.');
            }
        });

        return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Find referrer if referral code is provided (from form or URL parameter)
            $referredBy = null;
            $referralCode = strtoupper(trim($data['referral_code'] ?? ''));
            
            if (!empty($referralCode)) {
                $referrer = User::where('referral_code', $referralCode)->first();
                if ($referrer && $referrer->id !== null) {
                    $referredBy = $referrer->id;
                }
            }

            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'referred_by' => $referredBy,
            ]);

            // Give bonus quota to referrer if referral code is valid
            if ($referredBy) {
                $this->giveReferralBonus($referrer);
            }

            return $user;
        });
    }

    /**
     * Give referral bonus to referrer
     */
    protected function giveReferralBonus(User $referrer): void
    {
        try {
            $settings = ReferralSetting::getActive();
            
            if (!$settings || !$settings->is_active) {
                return;
            }

            $userQuota = UserQuota::getOrCreateForUser($referrer->id);

            // Add bonus quota
            if ($settings->text_quota_bonus > 0) {
                $userQuota->addTextQuota($settings->text_quota_bonus);
            }

            if ($settings->multimedia_quota_bonus > 0) {
                $userQuota->addMultimediaQuota($settings->multimedia_quota_bonus);
            }

            Log::info('Referral bonus given', [
                'referrer_id' => $referrer->id,
                'text_quota_bonus' => $settings->text_quota_bonus,
                'multimedia_quota_bonus' => $settings->multimedia_quota_bonus,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to give referral bonus', [
                'referrer_id' => $referrer->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
