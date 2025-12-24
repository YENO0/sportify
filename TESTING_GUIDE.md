# Testing Guide for Integrated Sportify System

## Pre-Testing Checklist

### 1. **Clear All Caches**
```bash
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 2. **Check Database Connection**
```bash
# Verify SQLite database exists
php artisan migrate:status
```

If database is not set up:
```bash
php artisan migrate:fresh --seed
```

### 3. **Build Frontend Assets**
```bash
npm install
npm run build
# OR for development:
npm run dev
```

---

## System Testing Steps

### Phase 1: Basic System Health

#### ✅ **1. Start Development Server**
```bash
php artisan serve
```
- Expected: Server starts at `http://127.0.0.1:8000`
- Visit: `http://127.0.0.1:8000`
- Should see: Welcome page or redirect to login

#### ✅ **2. Test Database Connection**
```bash
php artisan tinker
# Then in tinker:
DB::connection()->getPdo();
# Should return: PDO object without errors
exit
```

#### ✅ **3. Verify Routes Load Correctly**
```bash
php artisan route:list
```
- Should list all routes without errors
- Check for any missing controllers or syntax errors

---

### Phase 2: Authentication & User Management

#### ✅ **4. Test Registration**
- Visit: `http://127.0.0.1:8000/register`
- **Check:**
  - Registration form displays correctly
  - Uses `layouts.auth` layout (centered card design)
  - Can create new user account
  - Redirects to appropriate page after registration

#### ✅ **5. Test Login**
- Visit: `http://127.0.0.1:8000/login`
- **Check:**
  - Login form displays correctly
  - Uses `layouts.auth` layout
  - Can login with valid credentials
  - Redirects to homepage after login

#### ✅ **6. Test Homepage (Role-Based)**
- Visit: `http://127.0.0.1:8000/homepage`
- **Check:**
  - Shows correct dashboard based on user role:
    - **Admin**: Admin dashboard with user stats
    - **Committee**: Committee dashboard
    - **Student**: Student dashboard with events
  - **Header displays correctly:**
    - "Sportify" brand link
    - "Welcome, [User Name]"
    - "Profile" link
    - "Log Out" button
  - **Only ONE header visible** (no duplicates)

#### ✅ **7. Test Profile**
- Visit: `http://127.0.0.1:8000/profile`
- **Check:**
  - Profile page loads
  - Can view user information
  - Can edit profile (if implemented)

#### ✅ **8. Test Logout**
- Click "Log Out" in header
- **Check:**
  - Successfully logs out
  - Redirects to login or welcome page

---

### Phase 3: Inventory Management Module

#### ✅ **9. Test Inventory Dashboard**
- Visit: `http://127.0.0.1:8000/inventory`
- **Check:**
  - Page loads without errors
  - Shows inventory list/dashboard
  - Header displays correctly (Sportify navbar)
  - Tailwind CSS styles applied correctly
  - Statistics display properly (if applicable)

#### ✅ **10. Test Create Equipment**
- Visit: `http://127.0.0.1:8000/inventory/create`
- **Check:**
  - Form displays correctly
  - Can create new equipment
  - Success message appears
  - Redirects after creation

#### ✅ **11. Test View Equipment**
- Visit: `http://127.0.0.1:8000/inventory/{id}` (use actual ID)
- **Check:**
  - Equipment details display
  - All information visible
  - Checkout/Return buttons work (if applicable)

#### ✅ **12. Test Edit Equipment**
- Visit: `http://127.0.0.1:8000/inventory/{id}/edit`
- **Check:**
  - Edit form pre-filled with data
  - Can update equipment
  - Changes save successfully

#### ✅ **13. Test Brands Management**
- Visit: `http://127.0.0.1:8000/brands`
- **Check:**
  - Brand list displays
  - Can create/edit/delete brands
  - Navigation works correctly

#### ✅ **14. Test Maintenance**
- Visit: `http://127.0.0.1:8000/maintenance`
- **Check:**
  - Maintenance records display
  - Can create maintenance records
  - Functions correctly

---

### Phase 4: Events Management Module (kuanyik integration)

#### ✅ **15. Test Events List (Committee)**
- Login as Committee member
- Visit: `http://127.0.0.1:8000/committee/events`
- **Check:**
  - Events list displays
  - Badges show correctly (badge-pending, badge-approved, etc.)
  - Buttons styled correctly (btn-primary, btn-success, etc.)
  - Table displays properly
  - Header shows correctly

#### ✅ **16. Test Create Event**
- Visit: `http://127.0.0.1:8000/events/create`
- **Check:**
  - Form displays correctly
  - All fields present (event_name, event_description, dates, etc.)
  - Can create new event
  - Form validation works

#### ✅ **17. Test View Event**
- Visit: `http://127.0.0.1:8000/events/{id}`
- **Check:**
  - Event details display
  - All CSS classes from kuanyik work:
    - Badges (badge-approved, badge-pending, etc.)
    - Buttons (btn-primary, btn-success, etc.)
    - Cards, tables, forms styled correctly
  - Registration functionality works (if applicable)

#### ✅ **18. Test Edit Event**
- Visit: `http://127.0.0.1:8000/events/{id}/edit`
- **Check:**
  - Edit form displays
  - Can update event
  - Changes save successfully

#### ✅ **19. Test Event Approval (Admin)**
- Login as Admin
- Visit admin events page
- **Check:**
  - Can approve/reject events
  - Status updates correctly
  - Badges update appropriately

---

### Phase 5: Admin Features

#### ✅ **20. Test User Management (Admin Only)**
- Login as Admin
- Visit: `http://127.0.0.1:8000/admin/users`
- **Check:**
  - User list displays
  - Can create/edit/delete users
  - Role management works

#### ✅ **21. Test Committee Creation (Admin Only)**
- Visit: `http://127.0.0.1:8000/admin/committee/create`
- **Check:**
  - Form displays
  - Can create committee members
  - Works correctly

---

### Phase 6: UI/UX Testing

#### ✅ **22. Test Header Navigation**
- Navigate to multiple pages
- **Check:**
  - Header appears on all pages using `layouts.app`
  - "Sportify" brand link works (links to homepage)
  - "Welcome, [Name]" displays correctly
  - "Profile" link works
  - "Log Out" button works
  - Header is sticky (stays at top when scrolling)
  - Only ONE header visible on each page

#### ✅ **23. Test Layout Consistency**
- Visit multiple pages:
  - `/inventory`
  - `/homepage`
  - `/events`
  - `/brands`
- **Check:**
  - All pages use consistent layout
  - Header appears on all pages
  - Styles are consistent
  - No broken CSS

#### ✅ **24. Test Responsive Design**
- Resize browser window
- **Check:**
  - Layout adapts to different screen sizes
  - Navigation remains usable
  - Forms remain functional

---

### Phase 7: Error Handling

#### ✅ **25. Test Error Messages**
- Try invalid operations (e.g., invalid login, missing required fields)
- **Check:**
  - Error messages display correctly
  - Uses alert styling from layout
  - Messages are user-friendly

#### ✅ **26. Test 404 Errors**
- Visit non-existent page: `http://127.0.0.1:8000/nonexistent`
- **Check:**
  - 404 page displays
  - Doesn't break layout

---

### Phase 8: Integration Points

#### ✅ **27. Test Role-Based Access**
- Login with different roles (Admin, Committee, Student)
- **Check:**
  - Each role sees appropriate dashboard
  - Admin-only routes require admin role
  - Committee routes work for committee members
  - Unauthorized access redirects appropriately

#### ✅ **28. Test Session Management**
- Login and navigate between pages
- **Check:**
  - Session persists across pages
  - User stays logged in
  - Logout properly clears session

---

## Common Issues to Watch For

### ❌ **Issue: Duplicate Headers**
- **Symptom**: Two headers visible on homepage
- **Solution**: Ensure homepage views don't have duplicate `<nav>` elements

### ❌ **Issue: CSS Not Loading**
- **Symptom**: Pages look unstyled
- **Solution**: Run `npm run build` or `npm run dev`

### ❌ **Issue: Route Not Found**
- **Symptom**: 404 errors on known routes
- **Solution**: Run `php artisan route:clear` and `php artisan optimize:clear`

### ❌ **Issue: Database Errors**
- **Symptom**: SQL errors or missing tables
- **Solution**: Run `php artisan migrate:fresh --seed`

### ❌ **Issue: Undefined Classes/Models**
- **Symptom**: "Class not found" errors
- **Solution**: Run `composer dump-autoload`

---

## Quick Test Script

Run these commands to quickly verify system health:

```bash
# 1. Clear all caches
php artisan optimize:clear

# 2. Check routes
php artisan route:list > routes_check.txt

# 3. Check database
php artisan migrate:status

# 4. Build assets
npm run build

# 5. Start server
php artisan serve
```

---

## Expected Test Results Summary

✅ **All Pages Should:**
- Display correctly with proper styling
- Show single header (Sportify navbar)
- Have consistent layout
- Handle errors gracefully

✅ **Authentication Should:**
- Allow registration/login
- Redirect appropriately
- Maintain sessions
- Enforce role-based access

✅ **Modules Should:**
- Inventory: Full CRUD operations work
- Events: Create, view, edit, approve/reject work
- Brands: CRUD operations work
- Maintenance: Records management works
- Admin: User/committee management works

---

## Reporting Test Results

Document any issues found:
1. **Page/Route**: Which page had the issue
2. **Expected Behavior**: What should have happened
3. **Actual Behavior**: What actually happened
4. **Error Messages**: Any error messages or console errors
5. **Steps to Reproduce**: How to trigger the issue

