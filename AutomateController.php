<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Adjust based on your model name
use Illuminate\Support\Facades\DB;

class AutomateController extends Controller
{
    /**
     * Get products list for N8N workflow
     * Returns all active products with country and city for activity matching
     * 
     * Endpoint: GET /api/automate/products/list
     */
    public function getProductsList()
    {
        try {
            // Fetch all active products with required fields
            $products = Product::select([
                'id as product_id',
                'country',
                'city',
                'title',  // Change to 'name' or 'product_name' based on your DB column
                'sub_category_id',
                'category_id'
            ])
            ->where('status', 'active')  // Only active products
            ->whereNotNull('country')    // Must have country
            ->whereNotNull('city')       // Must have city
            ->orderBy('country')
            ->orderBy('city')
            ->get();
            
            // Transform data to ensure consistent format
            $formattedProducts = $products->map(function ($product) {
                return [
                    'product_id' => $product->product_id,
                    'country' => trim($product->country),
                    'city' => trim($product->city),
                    'activity' => trim($product->title),  // N8N expects 'activity' field
                    'title' => trim($product->title),     // Keep original for reference
                    'sub_category_id' => $product->sub_category_id ?? $product->category_id
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $formattedProducts,
                'count' => $formattedProducts->count(),
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get products by country (optional - for filtering)
     * 
     * Endpoint: GET /api/automate/products/list?country=Sri Lanka
     */
    public function getProductsListByCountry(Request $request)
    {
        try {
            $query = Product::select([
                'id as product_id',
                'country',
                'city',
                'title',
                'sub_category_id',
                'category_id'
            ])
            ->where('status', 'active')
            ->whereNotNull('country')
            ->whereNotNull('city');
            
            // Filter by country if provided
            if ($request->has('country')) {
                $query->where('country', $request->country);
            }
            
            // Filter by city if provided
            if ($request->has('city')) {
                $query->where('city', $request->city);
            }
            
            $products = $query->orderBy('country')->orderBy('city')->get();
            
            $formattedProducts = $products->map(function ($product) {
                return [
                    'product_id' => $product->product_id,
                    'country' => trim($product->country),
                    'city' => trim($product->city),
                    'activity' => trim($product->title),
                    'title' => trim($product->title),
                    'sub_category_id' => $product->sub_category_id ?? $product->category_id
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedProducts,
                'count' => $formattedProducts->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get cities grouped by country (useful for debugging)
     * 
     * Endpoint: GET /api/automate/products/cities
     */
    public function getCitiesByCountry()
    {
        try {
            $cities = Product::select('country', 'city')
                ->where('status', 'active')
                ->whereNotNull('country')
                ->whereNotNull('city')
                ->groupBy('country', 'city')
                ->orderBy('country')
                ->orderBy('city')
                ->get()
                ->groupBy('country')
                ->map(function ($group) {
                    return $group->pluck('city')->unique()->values();
                });
            
            return response()->json([
                'success' => true,
                'data' => $cities,
                'total_countries' => $cities->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching cities',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
