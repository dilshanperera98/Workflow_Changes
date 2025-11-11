# How to Add/Edit Activities - Simple Guide

## ✨ Super Easy Method - Edit Code Node Directly!

No Excel files needed! Just edit the code in N8N.

### Step-by-Step:

1. **Open your N8N workflow** (`Test.json`)

2. **Find the "Read Activities Excel List" node** (it's after "Sanitize Activities Data")

3. **Click on it to open**

4. **You'll see JavaScript code with an array like this:**

```javascript
const excelActivitiesList = [
  { country: "Sri Lanka", city: "Colombo", activity: "Galle Face Green" },
  { country: "Sri Lanka", city: "Colombo", activity: "National Museum of Colombo" },
  // ... more activities
];
```

5. **Edit the activities:**

### To ADD a new activity:
```javascript
// Add this line to the array
{ country: "Sri Lanka", city: "Nuwara Eliya", activity: "Gregory Lake" },
```

### To REMOVE an activity:
```javascript
// Delete the line or comment it out with //
// { country: "Sri Lanka", city: "Colombo", activity: "Old Activity" },
```

### To MODIFY an activity:
```javascript
// Just change the text
{ country: "Sri Lanka", city: "Colombo", activity: "NEW NAME HERE" },
```

### To ADD a NEW CITY:
```javascript
// Add all activities for that city
{ country: "Sri Lanka", city: "Nuwara Eliya", activity: "Gregory Lake" },
{ country: "Sri Lanka", city: "Nuwara Eliya", activity: "Victoria Park" },
{ country: "Sri Lanka", city: "Nuwara Eliya", activity: "Tea Plantation Visit" },
```

### To ADD a NEW COUNTRY:
```javascript
// Add activities for a new country
{ country: "Maldives", city: "Male", activity: "Grand Friday Mosque" },
{ country: "Maldives", city: "Male", activity: "Artificial Beach" },
{ country: "Maldives", city: "Hulhumale", activity: "Beach Walk" },
```

6. **Save the node** (click outside or save button)

7. **Done!** The workflow will now use your updated activities.

## Format Rules

**IMPORTANT**: Follow this exact format:

```javascript
{ country: "Country Name", city: "City Name", activity: "Activity Name" },
```

- Each entry MUST be on its own line
- Each entry MUST end with a comma `,` (except the last one)
- Use double quotes `"` around text
- Spelling of country/city MUST match what AI generates (case-insensitive matching)

## Common Mistakes to Avoid

❌ **Missing comma**:
```javascript
{ country: "Sri Lanka", city: "Colombo", activity: "Activity 1" }  // Missing comma!
{ country: "Sri Lanka", city: "Colombo", activity: "Activity 2" },
```

✅ **Correct**:
```javascript
{ country: "Sri Lanka", city: "Colombo", activity: "Activity 1" },  // Has comma
{ country: "Sri Lanka", city: "Colombo", activity: "Activity 2" },
```

❌ **Wrong quotes**:
```javascript
{ country: 'Sri Lanka', city: 'Colombo', activity: 'Activity' },  // Single quotes
```

✅ **Correct**:
```javascript
{ country: "Sri Lanka", city: "Colombo", activity: "Activity" },  // Double quotes
```

## Quick Copy-Paste Templates

### For Sri Lanka Cities:

```javascript
// Colombo
{ country: "Sri Lanka", city: "Colombo", activity: "Activity Name Here" },

// Kandy  
{ country: "Sri Lanka", city: "Kandy", activity: "Activity Name Here" },

// Galle
{ country: "Sri Lanka", city: "Galle", activity: "Activity Name Here" },

// Ella
{ country: "Sri Lanka", city: "Ella", activity: "Activity Name Here" },

// Sigiriya
{ country: "Sri Lanka", city: "Sigiriya", activity: "Activity Name Here" },

// Nuwara Eliya
{ country: "Sri Lanka", city: "Nuwara Eliya", activity: "Activity Name Here" },
```

### For Thailand Cities:

```javascript
// Bangkok
{ country: "Thailand", city: "Bangkok", activity: "Activity Name Here" },

// Phuket
{ country: "Thailand", city: "Phuket", activity: "Activity Name Here" },

// Chiang Mai
{ country: "Thailand", city: "Chiang Mai", activity: "Activity Name Here" },
```

## Example: Adding 3 New Activities for Nuwara Eliya

**Before**:
```javascript
const excelActivitiesList = [
  { country: "Sri Lanka", city: "Colombo", activity: "Galle Face Green" },
  { country: "Sri Lanka", city: "Kandy", activity: "Temple of the Tooth" },
];
```

**After**:
```javascript
const excelActivitiesList = [
  { country: "Sri Lanka", city: "Colombo", activity: "Galle Face Green" },
  { country: "Sri Lanka", city: "Kandy", activity: "Temple of the Tooth" },
  
  // NEW: Added Nuwara Eliya activities
  { country: "Sri Lanka", city: "Nuwara Eliya", activity: "Gregory Lake" },
  { country: "Sri Lanka", city: "Nuwara Eliya", activity: "Victoria Park" },
  { country: "Sri Lanka", city: "Nuwara Eliya", activity: "Hakgala Botanical Garden" },
];
```

## Testing Your Changes

1. **Add some activities** for a city
2. **Run the workflow** with a request including that city
3. **Check the results** - your activities should appear first!

Example request: "5 days trip to Sri Lanka - Colombo and Nuwara Eliya"

Expected: Your Nuwara Eliya activities will be suggested!

## Tips

💡 **Group by city** for easier management
💡 **Add comments** to organize sections
💡 **Keep activity names clear** and descriptive
💡 **Match city spelling** exactly as AI generates (e.g., "Colombo" not "colombo")

## Need More Activities?

Just keep adding to the list! There's no limit. The format is:

```javascript
const excelActivitiesList = [
  // Entry 1
  { country: "Country1", city: "City1", activity: "Activity1" },
  
  // Entry 2
  { country: "Country1", city: "City1", activity: "Activity2" },
  
  // Entry 3
  { country: "Country1", city: "City2", activity: "Activity3" },
  
  // ... add as many as you want!
];
```

---

**Last Updated**: November 11, 2025
**Difficulty**: ⭐ Easy - Just copy/paste/edit!
**No Excel Required**: Everything in code node!
