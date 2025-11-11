# Laravel API Setup for N8N Workflow

## Quick Setup Guide

### 1. Add Route

Add this to `routes/api.php`:

```php
use App\Http\Controllers\AutomateController;

// N8N Workflow - Products List
Route::get('/automate/products/list', [AutomateController::class, 'getProductsList']);

// Optional: Filter by country/city
Route::get('/automate/products/cities', [AutomateController::class, 'getCitiesByCountry']);
```

### 2. Create/Update Controller

Copy the `AutomateController.php` file to:
```
app/Http/Controllers/AutomateController.php
```

Or add the methods to your existing AutomateController.

### 3. Update Database Column Names

In `AutomateController.php`, update these lines to match YOUR database:

```php
// Line 18-22: Update column names
$products = Product::select([
    'id as product_id',        // ← Your product ID column
    'country',                 // ← Your country column
    'city',                    // ← Your city column
    'title',                   // ← Change to 'name', 'product_name', etc.
    'sub_category_id',         // ← Your category ID column
    'category_id'
])
```

### 4. Update Model Name

If your model isn't called `Product`, change line 6:

```php
use App\Models\Product;  // ← Change to YourModel
```

And line 17:

```php
$products = Product::select([  // ← Change to YourModel
```

### 5. Test the Endpoint

```bash
# Test if endpoint works
curl http://192.16.26.182:8000/api/automate/products/list

# Should return JSON like:
{
  "success": true,
  "data": [
    {
      "product_id": 1,
      "country": "Sri Lanka",
      "city": "Colombo",
      "activity": "Colombo City Tour",
      "sub_category_id": 5
    }
  ]
}
```

### 6. Common Issues & Fixes

#### Issue: "Table 'products' doesn't exist"
**Fix**: Update model name and table reference

#### Issue: "Column 'title' not found"
**Fix**: Change `'title'` to your product name column:
- `'name'` or
- `'product_name'` or
- `'product_title'`

#### Issue: "No products returned"
**Fix**: Check:
1. Do products have `country` and `city` values?
2. Are products marked as `status = 'active'`?

Update this line if you don't use status:
```php
// Remove status filter
->where('status', 'active')  // ← Delete this line
```

## Database Schema Requirements

Your products table should have:

```sql
CREATE TABLE products (
    id INT PRIMARY KEY,
    country VARCHAR(100),      -- REQUIRED: e.g., "Sri Lanka"
    city VARCHAR(100),         -- REQUIRED: e.g., "Colombo"
    title VARCHAR(255),        -- REQUIRED: Product name
    sub_category_id INT,       -- REQUIRED: 1-10, 46
    status VARCHAR(50),        -- OPTIONAL: 'active', 'inactive'
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Sample Data

```sql
INSERT INTO products (country, city, title, sub_category_id, status) VALUES
('Sri Lanka', 'Colombo', 'Colombo Full Day City Tour 8 Hours', 5, 'active'),
('Sri Lanka', 'Colombo', 'Gangarama Temple - Entrance Ticket', 8, 'active'),
('Sri Lanka', 'Kandy', 'Temple of the Tooth Visit', 5, 'active'),
('Sri Lanka', 'Kandy', 'Royal Botanic Gardens Peradeniya', 5, 'active'),
('Sri Lanka', 'Galle', 'Galle Fort Walking Tour', 5, 'active'),
('Singapore', 'Singapore', 'Marina Bay Sands Observation Deck', 8, 'active'),
('Singapore', 'Singapore', 'Gardens by the Bay Ticket', 8, 'active');
```

## Testing Workflow Integration

### Step 1: Test API
```bash
curl http://192.16.26.182:8000/api/automate/products/list
```

Expected: List of all products with country, city, activity

### Step 2: Test in N8N

1. Open N8N workflow
2. Click "Read Activities Excel List" node
3. Click "Execute Node"
4. Check output - should show products from database

### Step 3: Test Full Workflow

Send test request:
```json
{
  "email_content": "I want a 5 days trip to Sri Lanka covering Colombo and Kandy",
  "user_id": 655
}
```

Expected output:
- Activities for Colombo from your database
- Activities for Kandy from your database

## Optional Enhancements

### 1. Cache Products (Performance)

```php
use Illuminate\Support\Facades\Cache;

public function getProductsList()
{
    $products = Cache::remember('n8n_products_list', 3600, function () {
        return Product::select([...])
            ->where('status', 'active')
            ->get();
    });
    
    // ... rest of code
}
```

### 2. Add Pagination

```php
public function getProductsList(Request $request)
{
    $perPage = $request->get('per_page', 1000);
    
    $products = Product::select([...])
        ->where('status', 'active')
        ->paginate($perPage);
    
    return response()->json([
        'success' => true,
        'data' => $products->items(),
        'meta' => [
            'current_page' => $products->currentPage(),
            'total' => $products->total()
        ]
    ]);
}
```

### 3. Add Search/Filter

```php
public function getProductsList(Request $request)
{
    $query = Product::select([...])
        ->where('status', 'active');
    
    // Filter by country
    if ($request->has('country')) {
        $query->where('country', $request->country);
    }
    
    // Filter by city
    if ($request->has('city')) {
        $query->where('city', 'LIKE', '%' . $request->city . '%');
    }
    
    $products = $query->get();
    // ... rest
}
```

## Troubleshooting Commands

```bash
# Clear Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Check routes
php artisan route:list | grep automate

# Check database connection
php artisan tinker
>>> Product::count()
>>> Product::select('country', 'city')->distinct()->get()
```

---

**Created**: November 11, 2025
**Purpose**: API endpoint for N8N workflow to fetch database products
**Endpoint**: GET /api/automate/products/list
**Response**: JSON array of products with country, city, activity
