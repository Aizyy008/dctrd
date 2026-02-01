<?php

namespace App\Http\Controllers\Panel\Store;

use App\Http\Controllers\Controller;
use App\Models\ProductFilter;
use App\Models\ProductSpecification;
use Illuminate\Http\Request;

class ProductFilterController extends Controller
{
    public function getByCategoryId($categoryId)
    {
        $defaultLocale = getDefaultLocale();

        $filters = ProductFilter::select('*')
            ->where('category_id', $categoryId)
            ->with([
                'options'  => function ($query) {
                    $query->orderBy('order', 'asc');
                },
            ])
            ->get();

        return response()->json([
            'filters' => $filters,
            'defaultLocale' => mb_strtolower($defaultLocale)
        ], 200);
    }

    public function getSpecifications($id)
    {
        $specifications = ProductSpecification::all();
        $specifications_categories = [];
        foreach ($specifications as $sp) {
            if (in_array($id,$sp->categories->pluck('category_id')->toArray())) {
                $specifications_categories[] = $sp->id;
            }
        }
        return response()->json(ProductSpecification::whereIn('id',$specifications_categories)->with('multiValues')->get());
    }
}
