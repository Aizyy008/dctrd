<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\DiscountUser;
use App\Models\Product;
use App\Models\SpecialOffer;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpecialOfferController extends Controller
{
    // +++++++++++++++++++ store() +++++++++++++++++++
    public function index(Request $request)
    {
        $this->authorize("panel_marketing_special_offers");

        $user = auth()->user();
        // ========== Webinars ==========
        $webinars = Webinar::select('id')
            ->where(function ($qu) use ($user) {
                $qu->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            })
            ->where('status', 'active')
            ->get();
        // ========== Products ==========
        $products = Product::with('translations')
                                ->select('id')
                                ->where(function ($qu) use ($user) 
                                {
                                    $qu->where('creator_id', $user->id);
                                })
                                ->where('status', 'active')
                                ->get()
                                ->map(function ($product) 
                                {
                                    $locale = app()->getLocale(); // Get current locale
                                    $title = optional($product->translate($locale))->title 
                                        ?? optional($product->translate('en'))->title 
                                        ?? 'Untitled'; // Default if no translation exists

                                    return [
                                        'id' => $product->id,
                                        'title' => $title,
                                    ];
                                });
        // ====== webinarIds ======
        $webinarIds = $webinars->pluck('id');
        // ====== productIds ======
        $productIds = $products->pluck('id');
        // ====== Query both webinars and products ======
        $query = SpecialOffer::where(function ($q) use ($webinarIds, $productIds) 
        {
            $q->whereIn('webinar_id', $webinarIds)
              ->orWhereIn('product_id', $productIds);
        });

        if ($request->get('active_discounts', '') == 'on') 
        {
            $query->where('status', 'active');
        }

        $specialOffers = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('panel.special_offers'),
            'specialOffers' => $specialOffers,
            'webinars' => $webinars,
            'products' => $products,
        ];

        return view(getTemplate() . '.panel.marketing.special_offers', $data);
    }
    // +++++++++++++++++++ store() +++++++++++++++++++
    public function store(Request $request)
    {
        $this->authorize("panel_marketing_special_offers");
        $data = $request->all();
        // Ensure 'item_id' is provided
        if (!isset($data['item_id'])) {
            return response([
                'code' => 422,
                'errors' => ['item_id' => trans('validation.required')],
            ], 422);
        }
        // extract "item" and "type"
        [$discountType, $itemId] = explode('_', $data['item_id']);
        // Validation rules
        $validator = Validator::make($data, [
            'name'      => 'required|string|max:255',
            'percent'   => 'required|numeric|min:0|max:100',
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'item_id'   => 'required|string',
        ]);
        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }
        // Check if there's an active special offer
        $activeSpecialOffer = null;
        if ($discountType === 'course') 
        {
            $activeSpecialOffer = Webinar::findOrFail($itemId)->activeSpecialOffer();
        } 
        elseif ($discountType === 'product') 
        {
            $activeSpecialOffer = SpecialOffer::where('product_id', $itemId)
                ->where('status', SpecialOffer::$active)
                ->where('from_date', '<=', now())
                ->where('to_date', '>=', now())
                ->exists();
        }
        if ($activeSpecialOffer) {
            return back()->with([
                'toast' => [
                    'title' => trans('public.request_failed'),
                    'msg'   => trans('update.this_item_has_active_special_offer'),
                    'status' => 'error'
                ]
            ]);
        }
        // Convert Dates to Unix Timestamps
        $fromDate = convertTimeToUTCzone($data['from_date'], getTimezone());
        $toDate = convertTimeToUTCzone($data['to_date'], getTimezone());
        // Store Special Offer
        SpecialOffer::create([
            'creator_id'    => auth()->id(),
            'name'          => $data["name"],
            'discount_type' => $discountType,
            'webinar_id'    => $discountType === 'course' ? $itemId : null,
            'product_id'    => $discountType === 'product' ? $itemId : null,
            'percent'       => $data["percent"],
            'status'        => SpecialOffer::$active,
            'created_at'    => time(), 
            'from_date'     => $fromDate->getTimestamp(),
            'to_date'       => $toDate->getTimestamp(),
        ]);
    
        return response()->json([
            'code' => 200
        ], 200);
    }
    

    public function disable(Request $request, $id)
    {
        $user = auth()->user();

        $specialOffer = SpecialOffer::where('id', $id)->first();

        if (!empty($specialOffer)) {
            $course = $specialOffer->webinar;

            if ($course->isOwner($user->id)) {
                $specialOffer->update([
                    'status' => SpecialOffer::$inactive
                ]);

                return response()->json([
                    'code' => 200
                ], 200);
            }
        }

        return response()->json([], 422);
    }
}
