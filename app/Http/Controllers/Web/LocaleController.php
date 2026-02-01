<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class LocaleController extends Controller
{
    public function setLocale(Request $request)
    {
        $this->validate($request, [
            'locale' => 'required'
        ]);

        $locale = $request->get('locale');
        $locale = localeToCountryCode(mb_strtoupper($locale), true);

        $generalSettings = getGeneralSettings();
        $userLanguages = $generalSettings['user_languages'] ?? [];

        if (in_array($locale, $userLanguages)) {
            if (auth()->check()) {
                $user = auth()->user();
                $user->update([
                    'language' => $locale
                ]);
            } else {
                Cookie::queue('user_locale', $locale, 30 * 24 * 60);
            }
        }

        $previousUrl = $request->get('previous_url');

        if (!empty($previousUrl)) {
            return redirect($previousUrl);
        }

        return redirect()->back();
    }
    // +++++++++++++++++++ setLocaleLanguageOverIp() +++++++++++++++++++
    public function setLocaleLanguageOverIp(Request $request)
    {
        try 
        {
            $this->validate($request, [
                'locale' => 'required|string'
            ]);

            $locale = $request->get('locale');
            Log::debug('setLocaleLanguageOverIp - Locale input: ' . $locale);
            $convertedLocale = mb_strtoupper($locale);
            Log::debug('setLocaleLanguageOverIp - Converted locale: ' . $convertedLocale);

            $generalSettings = getGeneralSettings();
            $userLanguages = $generalSettings['user_languages'] ?? [];
            // dd($convertedLocale);
            Log::debug('setLocaleLanguageOverIp - Available user languages: ' . json_encode($userLanguages));

            if (in_array($convertedLocale, $userLanguages)) 
            {
                if (auth()->check()) 
                {
                    $user = auth()->user();
                    $user->update([
                        'language' => $convertedLocale
                    ]);
                    Log::info('setLocaleLanguageOverIp - Updated user language to: ' . $convertedLocale);
                } 
                else 
                {
                    Cookie::queue('user_locale', $convertedLocale, 30 * 24 * 60);
                    Log::info('setLocaleLanguageOverIp - Set user_locale cookie to: ' . $convertedLocale);
                }
            } 
            else 
            {
                Log::warning('setLocaleLanguageOverIp - Invalid language selected: ' . $convertedLocale);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language selected: ' . $convertedLocale
                ], 422);
            }
            $previousUrl = $request->get('previous_url', url()->previous());
            return response()->json([
                'success' => true,
                'redirect_url' => $previousUrl
            ]);
        } 
        catch (\Exception $e) 
        {
            Log::error('setLocaleLanguageOverIp - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
