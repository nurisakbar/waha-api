<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessagePricingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PricingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\EnsureUserIsAdmin::class);
    }

    /**
     * Display pricing settings page
     */
    public function index()
    {
        $pricing = MessagePricingSetting::getActive();
        return view('admin.pricing.index', compact('pricing'));
    }

    /**
     * Update pricing settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'text_with_watermark_price' => 'required|numeric|min:0',
            'text_without_watermark_price' => 'required|numeric|min:0',
            'multimedia_price' => 'required|numeric|min:0',
            'watermark_text' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $pricing = MessagePricingSetting::getActive();
        
        // If updating existing active pricing, deactivate it and create new one
        if ($pricing->is_active && $request->has('is_active') && $request->is_active) {
            $pricing->update([
                'is_active' => false,
            ]);
            
            $pricing = MessagePricingSetting::create([
                'text_with_watermark_price' => $request->text_with_watermark_price,
                'text_without_watermark_price' => $request->text_without_watermark_price,
                'multimedia_price' => $request->multimedia_price,
                'watermark_text' => $request->watermark_text,
                'is_active' => true,
            ]);
        } else {
            $pricing->update($request->only([
                'text_with_watermark_price',
                'text_without_watermark_price',
                'multimedia_price',
                'watermark_text',
                'is_active',
            ]));
        }

        Log::info('Pricing settings updated', [
            'updated_by' => auth()->id(),
            'pricing_id' => $pricing->id,
        ]);

        return redirect()->route('admin.pricing.index')
            ->with('success', 'Pricing settings updated successfully!');
    }
}
