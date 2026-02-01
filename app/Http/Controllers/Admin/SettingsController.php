<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\traits\DeviceLimitSettings;
use App\Http\Controllers\Admin\traits\FinancialCurrencySettings;
use App\Http\Controllers\Admin\traits\FinancialOfflineBankSettings;
use App\Http\Controllers\Admin\traits\FinancialUserBankSettings;
use App\Http\Controllers\Admin\traits\NavbarButtonSettings;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\LanguageOverIpCountryMappingSetting;
use App\Models\LanguageOverIpPopupSetting;
use App\Models\NotificationTemplate;
use App\Models\OfflineBank;
use App\Models\PaymentChannel;
use App\Models\Setting;
use App\Models\Translation\SettingTranslation;
use App\Models\UserBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    use NavbarButtonSettings;
    use FinancialCurrencySettings;
    use FinancialOfflineBankSettings;
    use FinancialUserBankSettings;
    use DeviceLimitSettings;

    public function index()
    {
        removeContentLocale();

        $this->authorize('admin_settings');

        $data = [
            'pageTitle' => trans('admin/main.settings_title'),
        ];

        return view('admin.settings.index', $data);
    }

    public function page(Request $request, $page)
    {
        removeContentLocale();

        $this->authorize('admin_settings_' . $page);

        $settings = Setting::where('page', $page)
            ->get()
            ->keyBy('name');

        foreach ($settings as $setting) {
            $setting->value = json_decode($setting->value, true);
        }

        $data = [
            'pageTitle' => trans('admin/main.settings_title'),
            'settings' => $settings
        ];

        if ($page == 'notifications') {
            $data['notificationTemplates'] = NotificationTemplate::all();
        }

        if ($page == 'financial') {
            $paymentChannels = PaymentChannel::orderBy('created_at', 'desc')->paginate(10);
            $data['paymentChannels'] = $paymentChannels;

            if ($request->get('tab') == 'currency') {
                $data['currencies'] = Currency::query()
                    ->orderBy('order', 'asc')
                    ->get();
            }

            if ($request->get('tab') == 'offline_banks') {
                $data['offlineBanks'] = OfflineBank::query()
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            if ($request->get('tab') == 'user_banks') {
                $data['userBanks'] = UserBank::query()
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return view('admin.settings.' . $page, $data);
    }

    public function personalizationPage(Request $request, $name)
    {
        removeContentLocale();

        $this->authorize('admin_settings_personalization');

        $settings = Setting::where('name', $name)->first();

        $values = null;

        if (!empty($settings)) {
            $defaultLocal = getDefaultLocale();

            if (in_array($name, [Setting::$pageBackgroundName, Setting::$homeSectionsName, Setting::$themeFontsName, Setting::$themeColorsName, Setting::$othersPersonalizationName])) {
                $defaultLocal = Setting::$defaultSettingsLocale;
            }

            $locale = $request->get('locale', mb_strtolower($defaultLocal));

            storeContentLocale($locale, $settings->getTable(), $settings->id);

            if (!empty($settings->value)) {
                $values = json_decode($settings->value, true);

                $values['locale'] = mb_strtoupper($settings->locale);
            }
        }

        $data = [
            'pageTitle' => trans('admin/main.settings_title'),
            'values' => $values,
            'name' => $name
        ];

        return view('admin.settings.personalization', $data);
    }

    public function store(Request $request, $name)
    {

        if (!empty($request->get('name'))) {
            $name = $request->get('name');
        }

        $tmpValues = $request->get('value', null);
        $adminPanelUrl = (!empty($tmpValues) and !empty($tmpValues['admin_panel_url'])) ? $tmpValues['admin_panel_url'] : null;

        if (!empty($tmpValues)) {
            $locale = $request->get('locale', Setting::$defaultSettingsLocale); // default is "en"

            $values = [];
            foreach ($tmpValues as $key => $val) {
                if (is_array($val)) {
                    $values[$key] = array_filter($val);
                } else {
                    $values[$key] = $val;
                }
            }

            if ($name == 'referral') {

                $validator = Validator::make($values, [
                    'affiliate_user_commission' => 'nullable|numeric',
                ]);

                $validator->validate();
            } elseif ($name == 'general') {
                if (empty($values['user_languages']) or !is_array($values['user_languages'])) {
                    $values['content_translate'] = false;
                }
            } elseif ($name == 'maintenance_settings') {
                if (!empty($values['end_date'])) {
                    $values['end_date'] = convertTimeToUTCzone($values['end_date'], null)->getTimestamp();
                }
            }

            $values = json_encode($values);
            $values = str_replace('record', rand(1, 600), $values);

            $settings = Setting::updateOrCreate(
                ['name' => $name],
                [
                    'page' => $request->get('page', 'other'),
                    'updated_at' => time(),
                ]
            );

            SettingTranslation::updateOrCreate(
                [
                    'setting_id' => $settings->id,
                    'locale' => mb_strtolower($locale)
                ],
                [
                    'value' => $values,
                ]
            );

            cache()->forget('settings.' . $name);

            if ($name == 'general') {
                cache()->forget('settings.getDefaultLocale');
            }
        }

        if ($name == "security") { // after change admin panel url
            $url = !empty($adminPanelUrl) ? $adminPanelUrl : getAdminPanelUrl();
            $url .= '/settings/general';

            return redirect($url);
        }

        return back();
    }

    public function storeSeoMetas(Request $request)
    {
        $name = Setting::$seoMetasName;

        $this->authorize('admin_settings_seo');

        $data = $request->all();
        $locale = $request->get('locale', Setting::$defaultSettingsLocale);
        $newValues = $data['value'];
        $values = [];
        $settings = Setting::where('name', $name)->first();

        if (!empty($settings) and !empty($settings->value)) {
            $values = json_decode($settings->value);
        }

        if (!empty($newValues) and !empty($values)) {
            foreach ($newValues as $newKey => $newValue) {
                foreach ($values as $key => $value) {
                    if ($key == $newKey) {
                        $values->$key = $newValue;
                        unset($newValues[$key]);
                    }
                }
            }
        }

        if (!empty($newValues)) {
            $values = array_merge((array)$values, $newValues);
        }

        $settings = Setting::updateOrCreate(
            ['name' => $name],
            [
                'page' => 'seo',
                'updated_at' => time(),
            ]
        );

        SettingTranslation::updateOrCreate(
            [
                'setting_id' => $settings->id,
                'locale' => mb_strtolower($locale)
            ],
            [
                'value' => json_encode($values),
            ]
        );

        cache()->forget('settings.' . $name);

        return back();
    }

    public function editSocials($social_key)
    {
        removeContentLocale();

        $this->authorize('admin_settings_general');
        $settings = Setting::where('name', Setting::$socialsName)->first();

        if (!empty($settings)) {
            $values = json_decode($settings->value);

            foreach ($values as $key => $value) {
                if ($key == $social_key) {
                    $data = [
                        'pageTitle' => trans('admin/pages/setting.settings_socials'),
                        'social' => $value,
                        'socialKey' => $social_key,
                    ];

                    return view('admin.settings.general', $data);
                }
            }
        }

        abort(404);
    }

    public function deleteSocials($social_key, $locale = null)
    {
        $this->authorize('admin_settings_general');
        $settings = Setting::where('name', Setting::$socialsName)->first();

        if (empty($locale)) {
            $locale = Setting::$defaultSettingsLocale;
        }

        if (!empty($settings)) {
            $values = json_decode($settings->value);
            foreach ($values as $key => $value) {
                if ($key == $social_key) {
                    unset($values->$social_key);
                }
            }

            $settings = Setting::updateOrCreate(
                ['name' => Setting::$socialsName],
                [
                    'page' => 'general',
                    'updated_at' => time(),
                ]
            );

            SettingTranslation::updateOrCreate(
                [
                    'setting_id' => $settings->id,
                    'locale' => mb_strtolower($locale)
                ],
                [
                    'value' => json_encode($values),
                ]
            );

            cache()->forget('settings.' . Setting::$socialsName);

            return redirect(getAdminPanelUrl() . '/settings/general');
        }

        abort(404);
    }

    public function storeSocials(Request $request)
    {
        $this->authorize('admin_settings_general');
        $this->validate($request, [
            'value.*' => 'required',
        ]);

        $data = $request->all();
        $locale = $request->get('locale', Setting::$defaultSettingsLocale);
        $social = $data['social'];
        $values = [];

        $settings = Setting::where('name', Setting::$socialsName)->first();

        if ($social !== 'newSocial') {
            if (!empty($settings) and !empty($settings->value)) {
                $values = json_decode($settings->value);
                foreach ($values as $key => $value) {
                    if ($key == $social) {
                        $values->$key = $data['value'];
                    }
                }
            }
        } else {
            if (!empty($settings) and !empty($settings->value)) {
                $values = json_decode($settings->value);
            }
            $key = str_replace(' ', '_', $data['value']['title']);
            $newValue[$key] = $data['value'];
            $values = array_merge((array)$values, $newValue);
        }

        $settings = Setting::updateOrCreate(
            ['name' => Setting::$socialsName],
            [
                'page' => 'general',
                'updated_at' => time(),
            ]
        );

        SettingTranslation::updateOrCreate(
            [
                'setting_id' => $settings->id,
                'locale' => mb_strtolower($locale)
            ],
            [
                'value' => json_encode($values),
            ]
        );

        cache()->forget('settings.' . Setting::$socialsName);

        return redirect(getAdminPanelUrl() . '/settings/general');
    }

    public function storeCustomCssJs(Request $request)
    {
        $this->authorize('admin_settings_customization');

        $newValues = $request->get('value', null);
        $locale = $request->get('locale', Setting::$defaultSettingsLocale);
        $values = [];
        $settings = Setting::where('name', Setting::$customCssJsName)->first();

        if (!empty($settings) and !empty($settings->value)) {
            $values = json_decode($settings->value);
        }

        if (!empty($newValues) and !empty($values)) {
            foreach ($newValues as $newKey => $newValue) {
                foreach ($values as $key => $value) {
                    if ($key == $newKey) {
                        $values->$key = $newValue;
                        unset($newValues[$key]);
                    }
                }
            }
        }

        if (!empty($newValues)) {
            $values = array_merge((array)$values, $newValues);
        }

        if (!empty($values)) {
            $values = json_encode($values);

            $settings = Setting::updateOrCreate(
                ['name' => Setting::$customCssJsName],
                [
                    'page' => 'customization',
                    'updated_at' => time(),
                ]
            );

            SettingTranslation::updateOrCreate(
                [
                    'setting_id' => $settings->id,
                    'locale' => mb_strtolower($locale)
                ],
                [
                    'value' => $values,
                ]
            );

            cache()->forget('settings.' . Setting::$customCssJsName);

            return back();
        }
    }

    public function notificationsMetas(Request $request)
    {
        $this->authorize('admin_settings_notifications');
        $name = 'notifications';
        $values = $request->get('value', []);
        $locale = $request->get('locale', Setting::$defaultSettingsLocale);

        $settings = Setting::where('name', $name)->first();

        if (!empty($settings) and !empty($settings->value)) {
            $oldValues = json_decode($settings->value, true);

            $values = array_merge($oldValues, $values);
        }

        if (!empty($values)) {
            $values = array_filter($values);
            $values = json_encode($values);

            $settings = Setting::updateOrCreate(
                ['name' => $name],
                [
                    'page' => 'notifications',
                    'updated_at' => time(),
                ]
            );

            SettingTranslation::updateOrCreate(
                [
                    'setting_id' => $settings->id,
                    'locale' => mb_strtolower($locale)
                ],
                [
                    'value' => $values,
                ]
            );

            cache()->forget('settings.' . $name);
        }

        return back();
    }
    // +++++++++++++++++++++++ start : "LanguageOverIp" Tab +++++++++++++++++++++++
    // //////////////////// general settings ////////////////////
    // ========== storeLanguageOverIp() : store in "settings" table ==========
    public function storeLanguageOverIp(Request $request)
    {
        $this->validate($request, [
            'value.enable_language_over_ip' => 'nullable|boolean',
        ]);
        $data = $request->all();
        // Update settings table for enable_language_over_ip
        $settings = Setting::updateOrCreate(
            ['name' => 'language_over_ip'],
            [
                'page' => 'general',
                'value' => json_encode(['enable_language_over_ip' => $data['value']['enable_language_over_ip'] ?? 0]),
                'updated_at' => time(),
            ]
        );
        cache()->forget('settings.language_over_ip');
        return redirect(getAdminPanelUrl() . '/settings/general')->with('success', trans('admin/main.settings_saved'));
    }
    // //////////////////// country_mapping_settings ////////////////////
     // ============ storeMapping() : store in 'language_over_ip_country_mapping_settings" table : Ajax Request ============
     public function storeMapping(Request $request)
     {
         $userLanguages = getUserLanguagesLists();
         $this->validate($request, [
             'country_id' => 'required|exists:regions,id',
             'language' => 'required|in:' . implode(',', array_values($userLanguages)),
             'language_code' => 'required|in:' . implode(',', array_keys($userLanguages)),
         ]);
 
         // Check for existing mapping and update or create
         $mapping = LanguageOverIpCountryMappingSetting::updateOrCreate(
             ['country_id' => $request->country_id],
             [
                 'language' => $request->language,
                 'language_code' => $request->language_code,
             ]
         );
 
         // Return mapping data for UI update
         return response()->json([
             'success' => true,
             'message' => "Mapping Saved",
             'mapping' => [
                 'id' => $mapping->id,
                 'country' => $mapping->country->title ?? 'Unknown',
                 'language' => $mapping->language,
                 'language_code' => $mapping->language_code,
             ],
         ]);
     }
    // ============ deleteMapping() : delete from 'language_over_ip_country_mapping_settings" table : Ajax Request ============
    public function deleteMapping($mappingId)
    {
        $mapping = LanguageOverIpCountryMappingSetting::find($mappingId);
        if ($mapping)
        {
            $mapping->delete();
            return response()->json([
                'success' => true,
                'message' => trans('admin/main.mapping_deleted'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Mapping not found.',
        ], 404);
    }
    // //////////////////// popup_settings ////////////////////
    // ============ storePopupSetting() ============
    public function storePopupSetting(Request $request)
    {
        // dd($request);
        $this->validate($request, [
            'language' => 'required|in:' . implode(',', array_values(getUserLanguagesLists())),
            'notification_title' => 'nullable|string|max:255',
            'notification_text' => 'nullable|string|max:255',
            'confirm_button_text' => 'nullable|string|max:255',
            'cancel_button_text' => 'nullable|string|max:255',
            'action_type' => 'required|boolean',
        ]);

        $setting = LanguageOverIpPopupSetting::updateOrCreate(
            ['language' => $request->language],
            [
                'notification_title' => $request->notification_title,
                'notification_text' => $request->notification_text,
                'confirm_button_text' => $request->confirm_button_text,
                'cancel_button_text' => $request->cancel_button_text,
                'action_type' => $request->action_type,
            ]
        );
        return response()->json([
            'success' => true,
            'message' => "Popup Settings Saved Successfully",
        ]);
    }
    // +++++++++++++++++++++++ end : "LanguageOverIp" Tab +++++++++++++++++++++++
}
