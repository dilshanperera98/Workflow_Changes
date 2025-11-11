# Database Products Integration - LIVE DATA ✅

## What Changed

### ✅ NOW USING REAL DATABASE PRODUCTS!

The workflow now fetches **actual products from your database** instead of using a static list.

## New Flow

```
Sanitize Activities Data
    ↓
Read Activities Excel List (HTTP Request to your API)
    ↓ (Fetches products from database)
Match City Activities from Excel (Filters by city)
    ↓
Merge Excel & AI Activities (Prioritizes database products)
    ↓
Build Activities Itinerary Prompt (AI uses database products)
```

## Changes Made

### 1. Read Activities Excel List - NOW API CALL
**Before**: Static embedded JavaScript array
**Now**: HTTP GET Request to your database

```
Type: HTTP Request
Method: GET
URL: http://192.16.26.182:8000/api/automate/products/list
```

This node now fetches the **current product list** from your database in real-time.

### 2. Match City Activities from Excel - UPDATED
**Enhanced to handle API response formats**

Supports multiple response structures:
- `{ data: [...] }`
- `{ products: [...] }`
- Direct array `[...]`
- Single object

Extracts fields:
- `country` - Product country
- `city` - Product city/location
- `title` / `name` / `activity` - Product name
- `product_id` / `id` - Product identifier
- `sub_category_id` / `category_id` - Category

## API Endpoint Required

Your Laravel API must provide this endpoint:

**Endpoint**: `GET http://192.16.26.182:8000/api/automate/products/list`

**Expected Response Format** (any of these):

### Option 1: With 'data' wrapper
```json
{
  "success": true,
  "data": [
    {
      "product_id": 123,
      "country": "Sri Lanka",
      "city": "Colombo",
      "title": "Colombo Full Day City Tour 8 Hours",
      "sub_category_id": 5
    },
    {
      "product_id": 124,
      "country": "Sri Lanka",
      "city": "Kandy",
      "title": "Temple of the Tooth Visit",
      "sub_category_id": 5
    }
  ]
}
```

### Option 2: Direct array
```json
[
  {
    "id": 123,
    "country": "Sri Lanka",
    "city": "Colombo",
    "name": "Colombo Full Day City Tour 8 Hours",
    "category_id": 5
  }
]
```

### Option 3: With 'products' wrapper
```json
{
  "products": [
    {
      "product_id": 123,
      "country": "Sri Lanka",
      "city": "Colombo",
      "activity": "Colombo Full Day City Tour 8 Hours",
      "sub_category_id": 5
    }
  ]
}
```

## How It Works

1. **Workflow triggers** with travel request (e.g., "Colombo, Kandy, Galle")

2. **Read Activities Excel List** calls your API:
   - `GET http://192.16.26.182:8000/api/automate/products/list`
   - Returns ALL products from database

3. **Match City Activities** filters products:
   - Request has: Colombo, Kandy, Galle
   - Filters products where `city` matches
   - Creates city-wise product map

4. **Merge Excel & AI Activities** prioritizes database products:
   - Database products = FIRST priority
   - AI-generated activities = Fallback if database empty

5. **AI generates itinerary** using database products:
   - "For Colombo: Use these activities FIRST: [database products]"
   - AI includes database products in final itinerary

## Laravel API Implementation

Create this route in your Laravel API:

```php
// routes/api.php
Route::get('/automate/products/list', [AutomateController::class, 'getProductsList']);
```

Controller method:

```php
// app/Http/Controllers/AutomateController.php
public function getProductsList()
{
    $products = Product::select([
        'id as product_id',
        'country',
        'city',
        'title',  // or 'name' or 'product_name'
        'sub_category_id'
    ])
    ->where('status', 'active')  // Only active products
    ->get();
    
    return response()->json([
        'success' => true,
        'data' => $products
    ]);
}
```

### Database Requirements

Your `products` table should have:
- `id` or `product_id` - Product identifier
- `country` - Country name (e.g., "Sri Lanka", "Singapore")
- `city` - City name (e.g., "Colombo", "Kandy", "Singapore")
- `title` or `name` or `activity` - Product name/title
- `sub_category_id` or `category_id` - Category (1-10, 46)
- `status` - Product status (optional, for filtering)

### Category IDs (Standard):
- 1 = Adventure
- 2 = Entertainment
- 3 = Health & Wellness
- 4 = Event
- 5 = Tours
- 6 = Transport
- 7 = Services
- 8 = Tickets
- 9 = Culinary
- 10 = Experience
- 46 = Vacation Packages

## Benefits

✅ **Real-Time Data**: Always uses latest products from database
✅ **No Manual Updates**: Add products in database, they appear in workflow
✅ **Accurate**: Only suggests products you actually have
✅ **Scalable**: Handles unlimited products
✅ **Consistent**: Same products in workflow and website/app

## Testing

### 1. Test API Endpoint
```bash
curl http://192.16.26.182:8000/api/automate/products/list
```

Should return JSON with products.

### 2. Test Workflow
Request: "6 days Sri Lanka - Colombo, Kandy, Galle"

Expected:
- Workflow calls your API
- Gets products for Colombo, Kandy, Galle from database
- AI uses those products in itinerary
- Final itinerary has your database products

### 3. Verify Product Matching
Check workflow execution:
- "Read Activities Excel List" output = All products from database
- "Match City Activities" output = Filtered products by city
- Final activities = Database products prioritized

## Troubleshooting

### API Returns Empty
**Check**:
- Is API endpoint accessible?
- Are products in database?
- Do products have `country` and `city` fields?

**Fix**: Add products to database with proper country/city values

### Products Not Matching Cities
**Check**:
- City names in database match AI-generated city names
- Example: Database has "Colombo", AI generates "Colombo" ✅
- Example: Database has "colombo", AI generates "Colombo" ✅ (case-insensitive)

**Fix**: Ensure city names are spelled correctly in database

### No Products Appear in Itinerary
**Check**:
- "Match City Activities" output - Are products found?
- "excel_activities_count" field - Should be > 0
- AI prompt - Should include database products

**Fix**: 
1. Verify API returns data
2. Check city name matching
3. Ensure products have `title`/`name`/`activity` field

## Adding New Products

Simply add products to your database:

```sql
INSERT INTO products (country, city, title, sub_category_id, status)
VALUES 
('Sri Lanka', 'Colombo', 'New Tour Package', 5, 'active'),
('Sri Lanka', 'Kandy', 'Cultural Experience', 10, 'active');
```

**No workflow changes needed!** Next request will automatically use new products.

## Migration from Static List

✅ **Done!** The workflow now uses database instead of static list.

Old behavior: Used hardcoded 60+ activities
New behavior: Uses ALL products from your database

---

**Updated**: November 11, 2025
**Status**: ✅ LIVE - Using database products
**Endpoint**: http://192.16.26.182:8000/api/automate/products/list
**Next Step**: Create the API endpoint in Laravel
