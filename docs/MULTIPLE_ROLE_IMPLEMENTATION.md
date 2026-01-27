# Multiple Role Selection Implementation

## Overview
Implemented multiple role selection feature for user management. Admins can now assign multiple roles to users and designate a primary role.

## Date
January 28, 2025

## Changes Made

### 1. Controller Updates (`/controllers/AdminController.php`)

#### `add_user()` Method
- **Changed**: Accepts `$_POST['roles']` array instead of single `$_POST['role']`
- **Added**: `$_POST['primary_role']` parameter for primary role designation
- **Logic**:
  - Validates all roles against allowed_roles array (8 roles including 'nurse')
  - Uses database transaction for atomic operations
  - Inserts user into `users` table with primary_role
  - Inserts each role into `user_roles` table with is_primary flag
  - Sets `granted_by` to current admin user ID
- **Success Message**: Shows count of roles assigned

#### `edit_user()` Method
- **Changed**: Fetches existing roles from `user_roles` table
- **Added**: Builds `$user_roles` array of current assigned roles
- **Added**: Identifies `$primary_role` from is_primary flag
- **Logic**:
  - On GET: Fetches and displays current roles
  - On POST: Deactivates old roles, inserts new roles
  - Updates both `users.role` (primary) and `user_roles` table
  - Uses ON DUPLICATE KEY UPDATE for role management
- **Passes to View**: `$user_roles`, `$primary_role`, `$available_roles`

### 2. Add User View (`/views/admin/add_user.php`)

#### UI Changes
- **Removed**: Single role dropdown
- **Added**: Grid of 8 role checkboxes with icons:
  - 🔐 Administrator
  - 👨‍⚕️ Doctor
  - 📋 Receptionist
  - 👩‍⚕️ Nurse (NEW)
  - 💰 Accountant
  - 💊 Pharmacist
  - 🔬 Lab Technician
  - 🩻 Radiologist (NEW)
- **Added**: Dynamic primary role radio button section (shows when multiple roles selected)
- **Added**: Error message for no role selection

#### JavaScript Features
- `handleRoleChange()`: Manages role selection logic
- **Auto-hide primary role section** when 0 or 1 role selected
- **Auto-show primary role section** when 2+ roles selected
- **Dynamic radio button generation** for selected roles
- **Auto-select first role** as primary by default
- **Form validation**: Prevents submit if no roles selected
- **Auto-submit primary role** if only one role (hidden input)

### 3. Edit User View (`/views/admin/edit_user.php`)

#### UI Changes
- **Added**: Current roles display section with color-coded badges
  - Yellow badge with ⭐ for primary role
  - Blue badges for additional roles
- **Added**: Grid of 8 role checkboxes (pre-checked based on current roles)
- **Added**: Dynamic primary role radio buttons (pre-selected current primary)
- **Visual feedback**: Checkboxes highlight in indigo when checked

#### JavaScript Features
- Same `handleRoleChange()` logic as add_user
- **Initialization**: Calls handleRoleChange() on page load
- **Pre-selects**: Current primary role in radio buttons
- **Updates styling**: Checkbox wrappers when roles change
- **Fallback**: Uses `users.role` if no user_roles entries exist

## Database Schema

### `user_roles` Table
```sql
CREATE TABLE user_roles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  role ENUM('admin','receptionist','doctor','lab_technician','accountant','pharmacist','radiologist','nurse'),
  is_primary TINYINT(1) DEFAULT 0,
  granted_by INT,
  granted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  is_active TINYINT(1) DEFAULT 1,
  FOREIGN KEY (user_id) REFERENCES users(id),
  UNIQUE KEY unique_user_role (user_id, role)
);
```

## Available Roles
1. **admin** - Full system access
2. **doctor** - Medical staff access
3. **receptionist** - Front desk operations
4. **nurse** - Patient care and ward management
5. **accountant** - Financial operations
6. **pharmacist** - Pharmacy management
7. **lab_technician** - Laboratory operations
8. **radiologist** - Radiology operations

## User Workflow

### Adding a New User
1. Admin navigates to `/admin/add_user`
2. Fills in personal information (name, email, phone)
3. Fills in username and password
4. **Checks one or more role checkboxes**
5. If multiple roles: **Selects primary role** via radio buttons
6. Clicks "Create User"
7. System creates user with all selected roles in database

### Editing an Existing User
1. Admin navigates to `/admin/edit_user/{id}`
2. Views current roles in color-coded badge display
3. Can check/uncheck roles to add/remove
4. If multiple roles: Can change primary role
5. Clicks "Update User"
6. System deactivates old roles and inserts new roles

## Technical Implementation Details

### Form Data Structure
```php
// POST data for add_user
$_POST = [
    'username' => 'jsmith',
    'password' => 'password123',
    'first_name' => 'John',
    'last_name' => 'Smith',
    'email' => 'john@example.com',
    'roles' => ['receptionist', 'nurse'],  // Array
    'primary_role' => 'receptionist'       // String
];
```

### Database Operations

#### Insert Multiple Roles
```php
foreach ($roles as $role) {
    $is_primary = ($role === $primary_role) ? 1 : 0;
    $stmt = $pdo->prepare("
        INSERT INTO user_roles (user_id, role, is_primary, granted_by) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $role, $is_primary, $_SESSION['user_id']]);
}
```

#### Update Roles
```php
// Deactivate old roles
UPDATE user_roles SET is_active = 0 WHERE user_id = ?;

// Insert new roles
INSERT INTO user_roles (user_id, role, is_primary, granted_by, granted_at) 
VALUES (?, ?, ?, ?, NOW())
ON DUPLICATE KEY UPDATE 
    is_primary = VALUES(is_primary),
    is_active = 1,
    granted_at = NOW();
```

## Validation Rules
1. **At least one role** must be selected
2. **Primary role** must be selected if multiple roles chosen
3. **All roles** must be valid (from allowed_roles array)
4. **Primary role** must be one of the selected roles
5. **Username** must be unique
6. **Password** minimum 6 characters (on creation)

## User Experience Features

### Visual Indicators
- ✅ Checkboxes for role selection
- 🔘 Radio buttons for primary role
- ⭐ Star icon for primary role badge
- 🎨 Color-coded role badges (yellow for primary, blue for additional)
- 💡 Hover effects on clickable role boxes
- ⚠️ Error message for validation failures

### Auto-Behavior
- Auto-hide primary role selector when 0-1 roles selected
- Auto-show primary role selector when 2+ roles selected
- Auto-select first role as primary by default
- Auto-submit primary role as hidden input if only one role
- Auto-highlight checkbox wrappers when checked

## Testing Checklist
- [ ] Add user with single role
- [ ] Add user with multiple roles (e.g., receptionist + nurse)
- [ ] Edit user to add additional roles
- [ ] Edit user to remove roles
- [ ] Edit user to change primary role
- [ ] Verify user_roles table updates correctly
- [ ] Verify login works with new users
- [ ] Verify dashboard redirect uses primary role
- [ ] Test validation (no roles selected)
- [ ] Test all 8 role types

## Notes
- **Nurse role** was already in database enum - just added to UI
- **Radiologist role** was already in database enum - now visible in UI
- Uses existing `user_roles` table - no schema changes needed
- Backward compatible - users with no user_roles entries fall back to `users.role`
- Primary role determines login redirect and default dashboard
- Additional roles can be used for permission checks in other modules

## Files Modified
1. `/controllers/AdminController.php` - Updated add_user() and edit_user() methods
2. `/views/admin/add_user.php` - New checkbox UI with dynamic primary role selector
3. `/views/admin/edit_user.php` - New checkbox UI with current role display

## Related Documentation
- `/docs/PAYMENT_WORKFLOW_EXPLANATION.md` - User role permissions
- `/database/zahanati.sql` - Database schema
