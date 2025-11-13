# City-Based Filtering Implementation

## Overview
Modified the N8N workflow to use **city-based filtering at the Google Sheets level** instead of fetching all rows and filtering in code. This makes the workflow more efficient and follows the requested pattern of querying Google Sheets based on the `destination` array.

---

## Changes Made

### 1. **Hotels Workflow** (Sheet3)

#### New Nodes Added:
**Split Cities for Hotels**
- **Type**: Code node
- **Position**: Between "Process Cities & Hotel Rating" and "Get row(s) in sheet1"
- **Function**: Splits the `destination` array into individual items, one per city
- **Output**: Multiple items, each containing the full data + a `city` field

#### Modified Nodes:

**Get row(s) in sheet1** (Sheet3 - Hotels)
- **Added Filter**: 
  ```json
  {
    "filtersUI": {
      "values": [
        {
          "lookupColumn": "City",
          "lookupValue": "={{ $json.city }}"
        }
      ]
    },
    "options": {
      "returnAllMatches": false
    }
  }
  ```
- **Behavior**: Now queries Google Sheets with city filter, returns only first matching row per city

**Merge Sheets Data1**
- **Simplified Logic**: No longer needs to filter data manually
- **New Approach**: Receives pre-filtered data from Google Sheets, just groups it by city
- **Removed**: Complex filtering logic, requested cities comparison

### 2. **Activities Workflow** (Sheet4)

#### New Nodes Added:
**Split Cities for Activities**
- **Type**: Code node
- **Position**: Between "Process Cities & Activities" and "Get row(s) in sheet"
- **Function**: Splits the `destination` array into individual items, one per city
- **Output**: Multiple items, each containing the full data + a `city` field

#### Modified Nodes:

**Get row(s) in sheet** (Sheet4 - Activities)
- **Added Filter**: 
  ```json
  {
    "filtersUI": {
      "values": [
        {
          "lookupColumn": "City",
          "lookupValue": "={{ $json.city }}"
        }
      ]
    },
    "options": {
      "returnAllMatches": false
    }
  }
  ```
- **Behavior**: Now queries Google Sheets with city filter, returns only first matching row per city

**Merge Sheets Data**
- **Simplified Logic**: No longer needs to filter data manually
- **New Approach**: Receives pre-filtered data from Google Sheets, just groups it by city
- **Removed**: Complex filtering logic, requested cities comparison

---

## Data Flow Diagram

### Hotels (OLD):
```
Process Cities & Hotel Rating
    ↓
Get row(s) in sheet1 (fetch ALL rows)
    ↓
Merge Sheets Data1 (filter by city in code)
    ↓
Sanitize Hotels Data
```

### Hotels (NEW):
```
Process Cities & Hotel Rating
    ↓
Split Cities for Hotels (create item per city)
    ↓
Get row(s) in sheet1 (query with city filter - parallel for each city)
    ↓
Merge Sheets Data1 (simple grouping, no filtering needed)
    ↓
Sanitize Hotels Data
```

### Activities (OLD):
```
Process Cities & Activities
    ↓
Get row(s) in sheet (fetch ALL rows)
    ↓
Merge Sheets Data (filter by city in code)
    ↓
Sanitize Activities Data
```

### Activities (NEW):
```
Process Cities & Activities
    ↓
Split Cities for Activities (create item per city)
    ↓
Get row(s) in sheet (query with city filter - parallel for each city)
    ↓
Merge Sheets Data (simple grouping, no filtering needed)
    ↓
Sanitize Activities Data
```

---

## How It Works

### Step 1: Split Cities
**Input**: 
```json
{
  "travel_data": {
    "destination": ["Colombo", "Kandy", "Galle"]
  }
}
```

**Output** (3 items):
```json
[
  { "travel_data": {...}, "city": "Colombo" },
  { "travel_data": {...}, "city": "Kandy" },
  { "travel_data": {...}, "city": "Galle" }
]
```

### Step 2: Google Sheets Query (Parallel)
For each city, Google Sheets node:
- Queries Sheet3/Sheet4 with filter: `City = "Colombo"` (or Kandy, Galle)
- Returns only the **first matching row** for that city
- Runs in parallel for all cities

### Step 3: Merge Results
Combines all Google Sheets responses into city-grouped structure:
```json
{
  "hotels_by_city": {
    "Colombo": [...],
    "Kandy": [...],
    "Galle": [...]
  }
}
```

---

## Benefits

✅ **Efficient Queries**: Only fetches data for requested cities (not all rows)  
✅ **Google Sheets Filtering**: Uses native filtering instead of code-based filtering  
✅ **First Row Only**: `returnAllMatches: false` ensures only first match per city  
✅ **Parallel Execution**: N8N executes Google Sheets queries in parallel for each city  
✅ **Simpler Code**: Merge nodes no longer need complex filtering logic  
✅ **Scalable**: Works for any number of cities in destination array  

---

## Example Request Flow

**User Request**: "I want a 6-day trip to Colombo, Kandy, and Galle"

**Destination Array**: `["Colombo", "Kandy", "Galle"]`

**Google Sheets Queries** (parallel):
1. Sheet3 WHERE City = "Colombo" → Returns first Colombo hotel
2. Sheet3 WHERE City = "Kandy" → Returns first Kandy hotel
3. Sheet3 WHERE City = "Galle" → Returns first Galle hotel

**Result**: Hotels embedded in each day's accommodation based on city

---

## Technical Details

### Split Node Code Pattern:
```javascript
const cities = processedData.travel_data?.destination || [];

// Create one item per city
const cityItems = cities.map(city => ({
  json: {
    ...processedData,
    city: city  // Used for Google Sheets filter
  }
}));

return cityItems;
```

### Google Sheets Filter Configuration:
```json
{
  "filtersUI": {
    "values": [
      {
        "lookupColumn": "City",
        "lookupValue": "={{ $json.city }}"
      }
    ]
  },
  "options": {
    "returnAllMatches": false  // CRITICAL: Only first match
  }
}
```

### Merge Node Pattern:
```javascript
const sheetsData = $input.all();
const processedData = sheetsData[0]?.json || {};

const dataByCity = {};

sheetsData.forEach(item => {
  const city = item.json.city;
  // Group by city...
  if (!dataByCity[city]) {
    dataByCity[city] = [];
  }
  dataByCity[city].push(item.json);
});
```

---

## Testing Checklist

- [x] Hotels workflow: Split cities node created
- [x] Hotels workflow: Google Sheets filter added
- [x] Hotels workflow: Merge logic simplified
- [x] Activities workflow: Split cities node created
- [x] Activities workflow: Google Sheets filter added
- [x] Activities workflow: Merge logic simplified
- [x] No JSON syntax errors
- [x] Node connections updated

**Next Steps**:
1. Test with multi-city itinerary (e.g., 3 cities)
2. Verify Google Sheets returns first row for each city
3. Check that hotels/activities are correctly embedded per day
4. Ensure cities not in Sheet3/Sheet4 return empty arrays

---

## Files Modified

1. **Testtoday.json** - Main workflow file with all changes

## New Nodes Created

1. **Split Cities for Hotels** (ID: `split-cities-hotels`)
2. **Split Cities for Activities** (ID: `split-cities-activities`)

---

Generated: 2025-11-13  
Updated Workflow: Testtoday.json  
Implementation: City-based Google Sheets filtering
