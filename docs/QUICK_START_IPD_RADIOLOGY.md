# QUICK START GUIDE - IPD & Radiology Implementation

## 🎯 Overview

Your system **already has multi-role infrastructure**! The `user_roles` junction table and `BaseController` authentication methods are fully functional. You just need to:

1. Add 'radiologist' and 'nurse' to role ENUMs ✅ (migration ready)
2. Create radiology and IPD tables ✅ (migration ready)
3. Build controllers and views (next steps)
4. Update routing and navigation
5. Assign multiple roles to users

---

## 🚀 Quick Start Commands

### Step 1: Run Database Setup (5 minutes)

**Option A: Automated setup script**
```bash
cd /var/www/html/KJ/database
./setup_ipd_radiology.sh
```
*Enter MySQL password when prompted*

**Option B: Manual migration**
```bash
mysql -u root -p zahanati < database/migrations/001_add_nurse_radiologist_roles.sql
mysql -u root -p zahanati < database/migrations/002_create_radiology_tables.sql
mysql -u root -p zahanati < database/migrations/003_create_ipd_tables.sql
mysql -u root -p zahanati < database/seeds/001_radiology_ipd_seed.sql
```

### Step 2: Verify Installation
```bash
mysql -u root -p zahanati -e "
SHOW TABLES LIKE '%radiology%';
SHOW TABLES LIKE '%ipd%';
SELECT COUNT(*) AS tests FROM radiology_tests;
SELECT COUNT(*) AS wards FROM ipd_wards;
"
```

Expected output:
- 4 radiology tables
- 5 IPD tables
- ~17 radiology tests
- 6 wards with ~30 beds

---

## 📋 What Gets Created

### Database Changes

**New Roles Added:**
- `radiologist` - for radiology department staff
- `nurse` - for nursing/IPD staff

**Radiology Tables:**
1. `radiology_test_categories` - X-Ray, Ultrasound, CT, MRI
2. `radiology_tests` - Individual tests with pricing
3. `radiology_test_orders` - Doctor orders for tests
4. `radiology_results` - Test findings and impressions

**IPD Tables:**
1. `ipd_wards` - Ward definitions (General, Private, ICU, etc.)
2. `ipd_beds` - Bed inventory with status tracking
3. `ipd_admissions` - Patient admission records
4. `ipd_progress_notes` - Daily nursing/doctor observations
5. `ipd_medication_admin` - Medication scheduling and administration

**Sample Data Loaded:**
- 17 radiology tests across 4 categories
- 6 wards (General A, General B, Private, ICU, Maternity, Pediatric)
- ~30 beds with varying daily rates
- Role permissions for radiologist and nurse

---

## 🧪 Testing Multi-Role Functionality

### Create a Test User with Multiple Roles

```sql
-- Create receptionist who is also a nurse (your real use case!)
INSERT INTO users (username, email, first_name, last_name, role, password_hash, is_active)
VALUES ('nurse.jane', 'jane@dispensary.com', 'Jane', 'Smith', 'receptionist', 
        '$2y$10$somehashedpassword', 1);

-- Get the user ID
SET @user_id = LAST_INSERT_ID();

-- Assign both roles
INSERT INTO user_roles (user_id, role, is_primary, granted_by) VALUES
(@user_id, 'receptionist', 1, 1),
(@user_id, 'nurse', 0, 1);

-- Verify
SELECT u.username, ur.role, ur.is_primary 
FROM users u 
JOIN user_roles ur ON u.id = ur.user_id 
WHERE u.username = 'nurse.jane';
```

Expected output:
```
username      | role          | is_primary
------------- | ------------- | ----------
nurse.jane    | receptionist  | 1
nurse.jane    | nurse         | 0
```

### Test Login and Role Access

1. Log in as `nurse.jane`
2. `$_SESSION['user_role']` should be `'receptionist'` (primary role)
3. `$_SESSION['user_roles']` should be `['receptionist', 'nurse']`
4. Call `$this->hasAnyRole(['nurse'])` → returns `true`
5. Call `$this->hasAnyRole(['doctor'])` → returns `false`

---

## 📁 Next Steps (After Running Setup)

### 1. Create Controllers (4-5 hours)

**RadiologistController.php:**
- Dashboard (pending orders, today's schedule)
- Orders listing (filterable by status)
- Perform test (mark in-progress)
- Record result (findings, impression, images)
- View result

**IpdController.php:**
- Dashboard (bed availability, active admissions)
- Bed management (wards, beds, status)
- Admit patient (assign bed, create admission)
- View admission (full patient details)
- Progress notes (nurse/doctor observations)
- Medication administration (nurse marking doses given)

### 2. Create Views (8-10 hours)

**Radiologist Views:**
- `views/radiologist/dashboard.php`
- `views/radiologist/orders.php`
- `views/radiologist/record_result.php`
- `views/radiologist/view_result.php`

**IPD Views:**
- `views/ipd/dashboard.php`
- `views/ipd/beds.php`
- `views/ipd/admit.php`
- `views/ipd/admissions.php`
- `views/ipd/view_admission.php`
- `views/ipd/progress_notes.php`
- `views/ipd/medication_admin.php`

### 3. Update Routing (30 minutes)

Add to `index.php`:
```php
// Radiologist routes
if ($role === 'radiologist' && $controller === 'radiologist') {
    require_once 'controllers/RadiologistController.php';
    $ctrl = new RadiologistController();
    // ... dispatch to methods
}

// IPD routes (accessible by nurse, receptionist, doctor, admin)
if (in_array($role, ['nurse','receptionist','doctor','admin']) && $controller === 'ipd') {
    require_once 'controllers/IpdController.php';
    $ctrl = new IpdController();
    // ... dispatch to methods
}
```

### 4. Update Navigation (1 hour)

Add to `views/layouts/main.php` sidebar:
```php
<?php if ($this->hasAnyRole(['radiologist'])): ?>
<!-- Radiology Menu -->
<li><a href="/radiologist/dashboard">Radiology</a></li>
<?php endif; ?>

<?php if ($this->hasAnyRole(['nurse', 'receptionist'])): ?>
<!-- IPD Menu -->
<li><a href="/ipd/dashboard">IPD (In-Patient)</a></li>
<?php endif; ?>
```

### 5. Add Role Switcher (1 hour)

Update header in `main.php`:
```php
<?php if (count($_SESSION['user_roles']) > 1): ?>
<select onchange="location.href='/switch-role/' + this.value">
    <?php foreach ($_SESSION['user_roles'] as $r): ?>
    <option value="<?= $r ?>" <?= $_SESSION['active_role'] === $r ? 'selected' : '' ?>>
        <?= ucfirst($r) ?>
    </option>
    <?php endforeach; ?>
</select>
<?php endif; ?>
```

### 6. Update Admin User Management (2 hours)

**views/admin/edit_user.php:**
- Add checkboxes for all 8 roles
- Add dropdown for primary role selection

**AdminController->edit_user():**
- Delete old user_roles entries
- Insert new role assignments
- Update users.role with primary role

---

## 💡 Key Implementation Notes

### Multi-Role Pattern Already Works!

Your `BaseController` already has:
```php
// Check if user has ANY of the provided roles
public function hasAnyRole($roles) {
    if (!is_array($roles)) $roles = [$roles];
    $userRoles = $this->getUserRoles(); // Gets from user_roles table
    return count(array_intersect($roles, $userRoles)) > 0;
}

// Require specific role(s) to access method
public function requireRole($roles) {
    if (!$this->hasAnyRole($roles)) {
        header('Location: /unauthorized');
        exit;
    }
}
```

### Usage in New Controllers

```php
class RadiologistController extends BaseController {
    public function __construct() {
        parent::__construct();
        $this->requireRole(['radiologist']); // Must have radiologist role
    }
    
    public function dashboard() {
        // Only accessible if user has radiologist role
        // ...
    }
}

class IpdController extends BaseController {
    public function __construct() {
        parent::__construct();
        // Allow multiple roles to access IPD
        $this->requireRole(['nurse', 'receptionist', 'doctor', 'admin']);
    }
}
```

### Your Real Use Case

Since your receptionist is also a nurse:

1. Admin assigns both roles via user_roles table
2. User logs in with primary role = 'receptionist'
3. User can access:
   - All receptionist functions (patient registration, revisits)
   - All IPD/nurse functions (admissions, vitals, medications)
4. Optional: Add role switcher so user can switch active role in UI

---

## 🔍 Troubleshooting

### "Role not found" error after migration
```sql
-- Verify ENUMs were updated
SHOW COLUMNS FROM users WHERE Field = 'role';
SHOW COLUMNS FROM user_roles WHERE Field = 'role';
```

### "Table doesn't exist" error
```sql
-- Verify tables were created
SHOW TABLES LIKE '%radiology%';
SHOW TABLES LIKE '%ipd%';
```

### User can't access IPD even with nurse role
```php
// Check session data
var_dump($_SESSION['user_roles']); // Should include 'nurse'

// Check database
SELECT * FROM user_roles WHERE user_id = YOUR_USER_ID;
```

---

## 📚 Documentation

**Full roadmap:** `docs/IPD_RADIOLOGY_IMPLEMENTATION_ROADMAP.md`
**Todo list:** See VS Code todo sidebar (12 tasks)

---

## ⏱️ Estimated Timeline

- **Database setup:** 5 minutes (you can do this NOW!)
- **Controllers:** 4-5 hours
- **Views:** 8-10 hours
- **Routing/Navigation:** 2 hours
- **Testing:** 2-3 hours
- **Total:** 16-20 hours

---

## 🎉 Ready to Start?

```bash
# Run this now to set up database!
cd /var/www/html/KJ/database
./setup_ipd_radiology.sh
```

After migration completes, start building controllers using the patterns in `IPD_RADIOLOGY_IMPLEMENTATION_ROADMAP.md`.

Good luck! 🚀
