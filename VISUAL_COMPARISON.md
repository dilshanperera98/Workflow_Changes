# Visual Comparison: Before vs After

## BEFORE (Inefficient - Fetch All Rows)

```
┌─────────────────────────────────┐
│ Process Cities & Hotel Rating   │
│ Output: {destination: [         │
│   "Colombo", "Kandy", "Galle"   │
│ ]}                              │
└────────────┬────────────────────┘
             │ Single item
             ↓
┌─────────────────────────────────┐
│ Get row(s) in sheet1            │
│ Query: SELECT * FROM Sheet3     │
│ Returns: ALL 27 hotels          │
│ (Colombo, Kandy, Galle, and     │
│  cities NOT in destination!)    │
└────────────┬────────────────────┘
             │ 27 items
             ↓
┌─────────────────────────────────┐
│ Merge Sheets Data1              │
│ Code filters 27 hotels down to  │
│ only 3 cities we need:          │
│ - Loop through 27 rows          │
│ - Check if city in destination  │
│ - Filter out 24 unwanted rows   │
│ - Keep only 3 matching cities   │
└────────────┬────────────────────┘
             │ Filtered data
             ↓
         Continue...
```

**Problems:**
- ❌ Fetches ALL 27+ hotels from Google Sheets
- ❌ Wastes bandwidth on unnecessary data
- ❌ Complex filtering logic in code
- ❌ Slower execution (more data to process)

---

## AFTER (Efficient - City-Based Filtering)

```
┌─────────────────────────────────┐
│ Process Cities & Hotel Rating   │
│ Output: {destination: [         │
│   "Colombo", "Kandy", "Galle"   │
│ ]}                              │
└────────────┬────────────────────┘
             │ Single item
             ↓
┌─────────────────────────────────┐
│ Split Cities for Hotels         │
│ Splits destination array into   │
│ individual items:               │
│ Item 1: {city: "Colombo"}       │
│ Item 2: {city: "Kandy"}         │
│ Item 3: {city: "Galle"}         │
└────────────┬────────────────────┘
             │ 3 items (parallel)
             ↓
      ┌──────┼──────┐
      │      │      │
      ↓      ↓      ↓
   ┌─────┐ ┌─────┐ ┌─────┐
   │City:│ │City:│ │City:│
   │Col- │ │Kan- │ │Gal- │
   │ombo│ │dy   │ │le   │
   └──┬──┘ └──┬──┘ └──┬──┘
      │      │      │
      ↓      ↓      ↓
┌─────────────────────────────────┐
│ Get row(s) in sheet1 (Parallel) │
│ Query 1: Sheet3 WHERE City="Colombo" → 1 row  │
│ Query 2: Sheet3 WHERE City="Kandy"   → 1 row  │
│ Query 3: Sheet3 WHERE City="Galle"   → 1 row  │
│ Returns: Only 3 rows (FIRST match per city)   │
└────────────┬────────────────────┘
             │ 3 items (exactly what we need)
             ↓
┌─────────────────────────────────┐
│ Merge Sheets Data1              │
│ Simple grouping:                │
│ - Receive 3 pre-filtered rows   │
│ - Group by city (no filtering)  │
│ - hotels_by_city = {            │
│     "Colombo": [...],           │
│     "Kandy": [...],             │
│     "Galle": [...]              │
│   }                             │
└────────────┬────────────────────┘
             │ Grouped data
             ↓
         Continue...
```

**Benefits:**
- ✅ Fetches only 3 hotels (one per city)
- ✅ Google Sheets does the filtering (more efficient)
- ✅ Parallel execution (faster)
- ✅ Simpler code (no filtering logic needed)
- ✅ Exact first row per city (`returnAllMatches: false`)

---

## Request Example

**Scenario**: User requests trip to Colombo, Kandy, Galle

### Before:
```
Google Sheets Query: SELECT * FROM Sheet3
Returns: [
  {city: "Colombo", hotel: "Berjaya"...},
  {city: "Colombo", hotel: "Best Western"...},
  {city: "Colombo", hotel: "Sapphire"...},
  {city: "Kandy", hotel: "Devon"...},
  {city: "Kandy", hotel: "Rivendell"...},
  {city: "Kandy", hotel: "City Hotel"...},
  {city: "Galle", hotel: "Koggala"...},
  {city: "Galle", hotel: "South Lake"...},
  {city: "Galle", hotel: "Long Beach"...},
  // ... 18 more hotels from other cities
]
Total: 27 rows

Code filters down to 9 rows (3 cities × 3 hotels)
```

### After:
```
Google Sheets Query 1: SELECT * FROM Sheet3 WHERE City="Colombo" LIMIT 1
Returns: {city: "Colombo", hotel: "Berjaya"...}

Google Sheets Query 2: SELECT * FROM Sheet3 WHERE City="Kandy" LIMIT 1
Returns: {city: "Kandy", hotel: "Devon"...}

Google Sheets Query 3: SELECT * FROM Sheet3 WHERE City="Galle" LIMIT 1
Returns: {city: "Galle", hotel: "Koggala"...}

Total: 3 rows (exactly what we need)
No code filtering needed!
```

---

## Performance Comparison

| Metric | Before | After | Improvement |
|--------|--------|-------|-------------|
| Rows Fetched | 27 | 3 | **90% reduction** |
| Filtering Location | Code | Google Sheets | **Native filtering** |
| Execution | Sequential | Parallel | **Faster** |
| Code Complexity | High (filtering logic) | Low (simple grouping) | **Simpler** |
| Bandwidth | High | Low | **More efficient** |

---

## Key Implementation Details

### 1. Split Node (`returnAllMatches: false`)
```javascript
// Creates 3 items from destination array
cities.map(city => ({
  json: {
    ...processedData,
    city: city  // Used for Google Sheets filter
  }
}));
```

### 2. Google Sheets Filter
```json
{
  "lookupColumn": "City",
  "lookupValue": "={{ $json.city }}"  // Dynamic per item
}
```

### 3. First Row Only
```json
{
  "options": {
    "returnAllMatches": false  // CRITICAL: Only first match
  }
}
```

This ensures for each city, only the FIRST matching row is returned from Google Sheets.

---

## Summary

**What Changed:**
- Added city-splitting nodes before Google Sheets queries
- Added Google Sheets filters to query by city
- Simplified merge logic (no manual filtering)

**Result:**
- More efficient queries (only fetch what's needed)
- Faster execution (parallel + less data)
- Simpler code (Google Sheets does the work)
- First row per city (as requested)

---

This pattern is now applied to **both** hotels (Sheet3) and activities (Sheet4) workflows! 🎉
