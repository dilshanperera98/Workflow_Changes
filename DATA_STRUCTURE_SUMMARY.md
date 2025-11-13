# N8N Workflow - Clean Data Structure Summary

## 📊 Overview

This document describes the final data structure for both Hotels and Activities workflows, ensuring clean and well-organized datasets for the APIs.

---

## 🏨 HOTELS WORKFLOW

### Data Fetching Strategy
**Excel/Google Sheets:** Fetch **FIRST ROW** per city (ONE hotel per city)

### Data Structure

#### 1. Merge Sheets Data1 Output:
```json
{
  "hotels_by_city": {
    "Colombo": {
      "hotel_name": "Shangri-La Colombo",
      "hotel_class": "5-Star",
      "aahaas_hotel_id": "12345",
      "apple_hotel_id": "ABC123",
      "basic_room_category": "Deluxe",
      "deluxe": "Yes",
      "country": "Sri Lanka",
      "city": "Colombo"
    },
    "Kandy": {
      "hotel_name": "Earl's Regency",
      "hotel_class": "4-Star",
      ...
    }
  },
  "hotel_metadata": {
    "requested_cities": ["Colombo", "Kandy", "Galle"],
    "total_sheet_hotels": 27,
    "filtered_hotels_count": 15,
    "selected_hotels_count": 3,
    "cities_with_hotels": ["Colombo", "Kandy", "Galle"],
    "cities_without_hotels": [],
    "note": "Only ONE hotel selected per city (first match)"
  }
}
```

**Key Points:**
- ✅ `hotels_by_city[cityName]` = **SINGLE OBJECT** (not array)
- ✅ Only **first matching hotel** per city
- ✅ Clean metadata with counts

---

#### 2. Parse Itinerary Response Output (Sent to Hotels API):
```json
{
  "success": true,
  "message": "Day-wise hotel itinerary generated successfully (ONE hotel per city)",
  "itinerary_title": "Sri Lanka Adventure",
  "cart_name": "Travel Cart",
  "cart_reference": "CART_1699876543_ABC123",
  "request_id": "REQ_1699876543_ABC123",
  "user_id": 655,
  "force_new_cart": true,
  
  "travel_data": {
    "destination": ["Colombo", "Kandy", "Galle"],
    "travel_dates": {
      "start": "2025-12-15",
      "end": "2025-12-22"
    },
    "duration": "8 days, 7 nights",
    "pax": {
      "adults": 2,
      "children": 1,
      "child_ages": [8]
    },
    "hotel_category": "5-Star",
    "hotel_star": 5,
    "meal_plan": "BB"
  },
  
  "hotels_by_city": {
    "Colombo": {
      "hotel_name": "Shangri-La Colombo",
      "hotel_class": "5-Star",
      "aahaas_hotel_id": "12345",
      ...
    },
    "Kandy": {...},
    "Galle": {...}
  },
  
  "itinerary": [
    {
      "day": 1,
      "date": "2025-12-15",
      "city": "Colombo",
      "pax": {
        "adults": 2,
        "children": 1,
        "child_ages": [8]
      },
      "accommodation": {
        "check_in": "2025-12-15",
        "check_out": "2025-12-18",
        "star_category": 5,
        "keywords": ["5-Star", "BB", "Colombo", "pool", "wifi"],
        "latitude": 6.9271,
        "longitude": 79.8612,
        "google_sheets_hotel": {
          "hotel_name": "Shangri-La Colombo",
          "hotel_class": "5-Star",
          "aahaas_hotel_id": "12345",
          "apple_hotel_id": "ABC123",
          "basic_room_category": "Deluxe",
          "deluxe": "Yes",
          "country": "Sri Lanka",
          "city": "Colombo"
        }
      }
    },
    {
      "day": 2,
      "date": "2025-12-16",
      "city": "Colombo",
      "pax": {...}
      // NO accommodation field - same city, handled by Laravel
    },
    {
      "day": 3,
      "date": "2025-12-17",
      "city": "Colombo",
      "pax": {...}
      // NO accommodation field
    },
    {
      "day": 4,
      "date": "2025-12-18",
      "city": "Kandy",
      "pax": {...},
      "accommodation": {
        "check_in": "2025-12-18",
        "check_out": "2025-12-21",
        "star_category": 4,
        "keywords": ["4-Star", "BB", "Kandy", "pool"],
        "latitude": 7.2906,
        "longitude": 80.6337,
        "google_sheets_hotel": {
          "hotel_name": "Earl's Regency",
          ...
        }
      }
    }
  ],
  
  "metadata": {
    "total_days": 8,
    "total_hotels": 3,
    "cities_with_hotels": ["Colombo", "Kandy", "Galle"],
    "hotels_count_per_city": {
      "Colombo": 1,
      "Kandy": 1,
      "Galle": 1
    },
    "generated_at": "2025-11-13T10:30:00Z",
    "generation_method": "OpenAI GPT-4o",
    "note": "Hotels: ONE per city (first match) - embedded in accommodation",
    "cart_reference": "CART_1699876543_ABC123",
    "cart_name": "Travel Cart",
    "request_id": "REQ_1699876543_ABC123"
  }
}
```

**Key Points:**
- ✅ `hotels_by_city` at root level for API reference
- ✅ Each city: **SINGLE hotel object**
- ✅ Hotel embedded in `accommodation.google_sheets_hotel`
- ✅ Accommodation only on **first day** of each city
- ✅ Clean metadata with per-city counts

---

## 🎯 ACTIVITIES WORKFLOW

### Data Fetching Strategy
**Excel/Google Sheets:** Fetch **ALL ROWS** per city (multiple activities per city)

### Data Structure

#### 1. Merge Sheets Data Output:
```json
{
  "activities_by_city": {
    "Colombo": [
      {
        "lifestyle_category": "Adventure",
        "must_do_activity": "Galle Face Green Walk",
        "aahaas_id": "ACT001",
        "apple_id": "APPLE_ACT001",
        "extra1": "Evening recommended",
        "extra2": "Free",
        "country": "Sri Lanka",
        "city": "Colombo"
      },
      {
        "lifestyle_category": "Culture",
        "must_do_activity": "National Museum Visit",
        "aahaas_id": "ACT002",
        ...
      },
      {
        "lifestyle_category": "Shopping",
        "must_do_activity": "Pettah Market Tour",
        ...
      }
    ],
    "Kandy": [
      {
        "lifestyle_category": "Culture",
        "must_do_activity": "Temple of the Tooth",
        ...
      },
      {
        "lifestyle_category": "Nature",
        "must_do_activity": "Botanical Gardens",
        ...
      }
    ]
  },
  "activity_metadata": {
    "requested_cities": ["Colombo", "Kandy", "Galle"],
    "total_sheet_activities": 45,
    "filtered_activities_count": 18,
    "activities_per_city": {
      "Colombo": 6,
      "Kandy": 5,
      "Galle": 7
    },
    "cities_with_activities": ["Colombo", "Kandy", "Galle"],
    "cities_without_activities": [],
    "note": "ALL activities fetched per city (multiple rows)"
  }
}
```

**Key Points:**
- ✅ `activities_by_city[cityName]` = **ARRAY** of all activities
- ✅ **ALL matching activities** per city (not just first)
- ✅ Metadata shows count per city

---

#### 2. Parse Activities Response Output (Sent to Activities API):
```json
{
  "success": true,
  "message": "Day-wise activities itinerary generated successfully",
  "itinerary_title": "Sri Lanka Adventure",
  "cart_name": "Travel Cart",
  "cart_reference": "CART_1699876543_ABC123",
  "request_id": "REQ_1699876543_ABC123",
  "user_id": 655,
  "force_new_cart": false,
  
  "travel_data": {
    "destination": ["Colombo", "Kandy", "Galle"],
    "travel_dates": {
      "start": "2025-12-15",
      "end": "2025-12-22"
    },
    "duration": "8 days, 7 nights",
    "pax": {
      "adults": 2,
      "children": 1,
      "child_ages": [8]
    },
    "activity_preferences": "popular_attractions",
    "budget_level": "mid-range"
  },
  
  "activities_by_city": {
    "Colombo": [
      {"lifestyle_category": "Adventure", "must_do_activity": "Galle Face Green Walk", ...},
      {"lifestyle_category": "Culture", "must_do_activity": "National Museum Visit", ...},
      {"lifestyle_category": "Shopping", "must_do_activity": "Pettah Market Tour", ...},
      {"lifestyle_category": "Food", "must_do_activity": "Street Food Tour", ...},
      {"lifestyle_category": "Nature", "must_do_activity": "Viharamahadevi Park", ...},
      {"lifestyle_category": "History", "must_do_activity": "Independence Square", ...}
    ],
    "Kandy": [
      {"lifestyle_category": "Culture", "must_do_activity": "Temple of the Tooth", ...},
      {"lifestyle_category": "Nature", "must_do_activity": "Botanical Gardens", ...},
      {"lifestyle_category": "Adventure", "must_do_activity": "Kandy Lake Walk", ...},
      {"lifestyle_category": "Culture", "must_do_activity": "Cultural Dance Show", ...},
      {"lifestyle_category": "Shopping", "must_do_activity": "Kandy City Center", ...}
    ],
    "Galle": [...]
  },
  
  "itinerary": [
    {
      "day": 1,
      "date": "2025-12-15",
      "city": "Colombo",
      "pax": {
        "adults": 2,
        "children": 1,
        "child_ages": [8]
      },
      "activities": [
        {
          "activity_name": "Galle Face Green",
          "category": "Tours",
          "sub_category_id": 5,
          "description": "Scenic promenade in Colombo",
          "duration": "2 hours",
          "latitude": 6.9271,
          "longitude": 79.8612,
          "best_time": "evening",
          "cost_level": "free"
        },
        {
          "activity_name": "National Museum",
          "category": "Tours",
          "sub_category_id": 5,
          "description": "Sri Lanka's largest museum",
          "duration": "2-3 hours",
          "latitude": 6.9074,
          "longitude": 79.8612,
          "best_time": "morning",
          "cost_level": "low"
        }
      ],
      "google_sheets_activities": [
        {"lifestyle_category": "Adventure", "must_do_activity": "Galle Face Green Walk", ...},
        {"lifestyle_category": "Culture", "must_do_activity": "National Museum Visit", ...},
        {"lifestyle_category": "Shopping", "must_do_activity": "Pettah Market Tour", ...},
        {"lifestyle_category": "Food", "must_do_activity": "Street Food Tour", ...},
        {"lifestyle_category": "Nature", "must_do_activity": "Viharamahadevi Park", ...},
        {"lifestyle_category": "History", "must_do_activity": "Independence Square", ...}
      ]
    },
    {
      "day": 2,
      "date": "2025-12-16",
      "city": "Colombo",
      "pax": {...},
      "activities": [
        {
          "activity_name": "Colombo City Tour",
          ...
        },
        {
          "activity_name": "Shopping at Pettah Market",
          ...
        }
      ],
      "google_sheets_activities": [
        // SAME 6 activities for Colombo
      ]
    }
  ],
  
  "metadata": {
    "total_days": 8,
    "total_activities": 14,
    "max_activities_per_day": 2,
    "activities_before_limit": 24,
    "activities_after_limit": 14,
    "activities_removed": 10,
    "cities_with_activities": ["Colombo", "Kandy", "Galle"],
    "activities_count_per_city": {
      "Colombo": 6,
      "Kandy": 5,
      "Galle": 7
    },
    "generated_at": "2025-11-13T10:30:00Z",
    "generation_method": "OpenAI GPT-4o",
    "note": "Activities: ALL rows per city (limited to 2 per day in itinerary) - embedded in each day",
    "cart_reference": "CART_1699876543_ABC123",
    "cart_name": "Travel Cart",
    "request_id": "REQ_1699876543_ABC123"
  }
}
```

**Key Points:**
- ✅ `activities_by_city` at root level with **ALL activities per city**
- ✅ Each day: **2 activities in itinerary** (limited by MAX_ACTIVITIES_PER_DAY)
- ✅ Each day: **ALL city activities in google_sheets_activities** (for reference)
- ✅ Metadata shows both total available and selected counts
- ✅ `force_new_cart: false` (adds to existing cart created by hotels)

---

## 🔄 Data Flow Comparison

| Aspect | Hotels Workflow | Activities Workflow |
|--------|----------------|---------------------|
| **Excel Fetch** | First row per city | All rows per city |
| **Data Type** | Single object | Array of objects |
| **Field Name** | `hotels_by_city[city]` = {...} | `activities_by_city[city]` = [...] |
| **Embedded In** | `accommodation.google_sheets_hotel` | `google_sheets_activities` array |
| **Appears** | First day of each city only | Every day |
| **API Root** | `hotels_by_city` object | `activities_by_city` object |
| **Cart Behavior** | `force_new_cart: true` | `force_new_cart: false` |

---

## 📋 API Input Structure

### Hotels API Input:
```json
{
  "user_id": 655,
  "cart_reference": "CART_...",
  "request_id": "REQ_...",
  "force_new_cart": true,
  "hotels_by_city": {
    "Colombo": {single_hotel_object},
    "Kandy": {single_hotel_object}
  },
  "itinerary": [
    {
      "day": 1,
      "accommodation": {
        "google_sheets_hotel": {single_hotel_object}
      }
    }
  ],
  "travel_data": {...},
  "metadata": {...}
}
```

### Activities API Input:
```json
{
  "user_id": 655,
  "cart_reference": "CART_...",  // SAME as hotels
  "request_id": "REQ_...",
  "force_new_cart": false,  // Add to existing cart
  "activities_by_city": {
    "Colombo": [activity1, activity2, activity3, ...],
    "Kandy": [activity1, activity2, ...]
  },
  "itinerary": [
    {
      "day": 1,
      "activities": [2 activities from OpenAI],
      "google_sheets_activities": [ALL activities for this city]
    }
  ],
  "travel_data": {...},
  "metadata": {...}
}
```

---

## ✅ Clean Dataset Features

### 1. **Hotels Dataset:**
- ✅ One hotel per city (clean, no duplicates)
- ✅ Embedded in accommodation object
- ✅ Available at root level for reference
- ✅ Metadata shows per-city counts

### 2. **Activities Dataset:**
- ✅ All activities per city (complete options)
- ✅ Limited to 2 per day in itinerary (configurable)
- ✅ Full list embedded in each day for reference
- ✅ Available at root level for reference
- ✅ Metadata shows both available and selected counts

### 3. **Both Datasets:**
- ✅ Same `cart_reference` for linking
- ✅ Clean, validated data (Sanitize nodes)
- ✅ Comprehensive metadata
- ✅ No unnecessary fields
- ✅ Proper error handling
- ✅ Clear field naming

---

## 🎯 Summary

| Dataset Type | Fetch Strategy | Data Structure | Use Case |
|-------------|----------------|----------------|----------|
| **Hotels** | First row per city | Single object | ONE hotel booking per city |
| **Activities** | All rows per city | Array of objects | Multiple activity options per city |

Both workflows provide **clean, well-structured data** to the APIs with:
- ✅ Proper filtering by requested cities
- ✅ Complete metadata for tracking
- ✅ Embedded data in itinerary for easy access
- ✅ Root-level data for API processing
- ✅ Same cart reference for unified booking

---

**Last Updated:** November 13, 2025  
**Workflow File:** `Test2025113new.json`  
**Status:** ✅ Clean Data Structure Implemented
