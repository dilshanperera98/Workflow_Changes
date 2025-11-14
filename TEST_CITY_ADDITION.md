# Testing City Addition Feature

## ✅ Changes Made

### 1. **Build Intent Detection Prompt** (Updated)
- Changed: `extend_nights=2` per city → `extend_nights=1` per city
- Examples updated to reflect 1 night per city

### 2. **Parse Intent & Modify Request** (Updated)
- Changed: Calculation from `totalCities × 2 nights` → `existingNights + 1 per new city`
- Output: Creates email with ALL cities (old + new) comma-separated

## 📝 Test Scenario

### First Request (Create Initial Itinerary):
```json
{
  "email_content": "I want to travel Sri Lanka 4 day trip",
  "user_id": "655",
  "editable": false
}
```

**Expected Result:**
- Cities: Colombo, Kandy, Galle (3 cities)
- Nights: 3 nights
- Dates: 2025-11-15 to 2025-11-18 (4 days)
- Cart created with hotels and activities for 3 cities

### Second Request (Add City):
```json
{
  "email_content": "I want to add another city in Yala",
  "user_id": "655",
  "editable": true,
  "cart_name": "[cart_name from first response]"
}
```

**Expected Processing:**
1. Load existing itinerary (Colombo, Kandy, Galle)
2. Detect: `is_adding_city=true`, `new_location="Yala"`, `extend_nights=1`
3. Build modified request: "I want a 5-day trip to Colombo, Kandy, Galle, Yala from 2025-11-15 to 2025-11-19. IMPORTANT: Include ALL 4 cities: Colombo, Kandy, Galle, Yala. Please re-optimize the travel route for these cities."
4. OpenAI Travel Parser receives ALL 4 cities
5. OpenAI re-optimizes route (e.g., Colombo → Yala → Ella → Galle or similar)
6. Generate hotels for ALL 4 cities
7. Generate activities for ALL 4 cities

**Expected Result:**
- Cities: Colombo, Kandy, Galle, Yala (ALL 4 cities)
- Nights: 4 nights (3 + 1)
- Dates: 2025-11-15 to 2025-11-19 (5 days)
- Route: Re-optimized for best travel order
- Hotels: Generated for ALL 4 cities
- Activities: Generated for ALL 4 cities
- Version: v2 of the cart

## 🔍 Key Points to Verify

1. **All Previous Cities Retained**: Colombo, Kandy, Galle must appear in final result
2. **New City Added**: Yala must appear in final result  
3. **Date Extension**: End date extends by 1 day (11-18 → 11-19)
4. **Route Optimization**: Cities may be reordered for best route
5. **Complete Data**: Hotels AND activities for ALL 4 cities

## 🐛 Debugging Tips

If cities are not being added:

1. **Check "Parse Intent & Modify Request" output:**
   - Verify `new_destinations` includes all 4 cities
   - Verify `cities_added` shows ["Yala"]
   - Verify `chatInput` contains all 4 city names

2. **Check "AI Agent" output:**
   - Verify it receives the text with all 4 cities

3. **Check "OpenAI Travel Parser" response:**
   - Verify `destination` array has all 4 cities
   - Verify `city_stays` has all 4 cities with correct nights distribution

4. **Check "Process Cities & Hotel Rating" / "Process Cities & Activities":**
   - Verify `destination` array has all 4 cities
   - Verify `city_details` has all 4 cities

## 📊 Expected City Distribution (4 nights, 4 cities)

- **Option 1** (Equal distribution): 1 night per city
  - Colombo: 1 night
  - Kandy: 1 night  
  - Galle: 1 night
  - Yala: 1 night

- **Option 2** (OpenAI optimized): May vary based on distances
  - Example: Colombo (1), Kandy (1), Yala (1), Galle (1)

The exact distribution will be determined by OpenAI based on the route optimization.
