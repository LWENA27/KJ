# IPD & RADIOLOGY MODULES - IMPLEMENTATION ROADMAP

**Date:** January 24, 2026  
**System:** KJ Dispensary Management System  
**Current Branch:** copilot/fetch-fields-from-database

---

## CURRENT SYSTEM ANALYSIS

### ✅ What Already Works
1. **Multi-role infrastructure exists:**
   - `user_roles` table with junction pattern (user_id, role, is_primary, granted_by)
   - `BaseController->requireRole()` supports array of roles
   - `BaseController->hasAnyRole()` checks user_roles table
   - `BaseController->getUserRoles()` returns all roles for a user

2. **Current roles in ENUM:**
   - admin, receptionist, doctor, lab_technician, accountant, pharmacist
   - **Missing:** radiologist, nurse (need to add)

3. **Database tables exist:**
   - patients, patient_visits, consultations, vital_signs
   - lab_tests, lab_test_orders, lab_results
   - medicines, prescriptions, payments
   - users, user_roles, role_permissions

4. **Visit types support IPD:**
   - patient_visits.visit_type ENUM already includes: 'consultation', 'lab_only', 'minor_service', 'ipd', 'medicine_pickup'

### ❌ What Needs to Be Built
1. **Radiology module** (complete new feature)
2. **IPD/Admission module** (complete new feature)
3. **Role switcher UI** (user can select active role)
4. **Nurse and Radiologist roles** (add to ENUMs)
5. **Navigation updates** (sidebar for new roles)

---

## IMPLEMENTATION PHASES

---

## PHASE 1: DATABASE SCHEMA (Priority: HIGH)

### Task 1.1: Add New Roles to ENUM
**File:** `database/migrations/001_add_nurse_radiologist_roles.sql`

```sql
-- Update all role ENUMs to include 'nurse' and 'radiologist'

ALTER TABLE `users` 
MODIFY COLUMN `role` ENUM(
  'admin','receptionist','doctor','lab_technician',
  'accountant','pharmacist','radiologist','nurse'
) NOT NULL;

ALTER TABLE `user_roles` 
MODIFY COLUMN `role` ENUM(
  'admin','receptionist','doctor','lab_technician',
  'accountant','pharmacist','radiologist','nurse'
) NOT NULL;

ALTER TABLE `role_permissions` 
MODIFY COLUMN `role` ENUM(
  'admin','receptionist','doctor','lab_technician',
  'accountant','pharmacist','radiologist','nurse'
) NOT NULL;
```

**Command to run:**
```bash
mysql -u root -p zahanati < database/migrations/001_add_nurse_radiologist_roles.sql
```

---

### Task 1.2: Create Radiology Tables
**File:** `database/migrations/002_create_radiology_tables.sql`

```sql
-- Radiology Test Categories
CREATE TABLE `radiology_test_categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `category_name` VARCHAR(100) NOT NULL,
  `category_code` VARCHAR(20) NOT NULL UNIQUE,
  `description` TEXT,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Radiology Tests
CREATE TABLE `radiology_tests` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `test_name` VARCHAR(200) NOT NULL,
  `test_code` VARCHAR(50) NOT NULL UNIQUE,
  `category_id` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `description` TEXT,
  `preparation_instructions` TEXT,
  `estimated_duration` INT COMMENT 'Minutes',
  `requires_contrast` TINYINT(1) DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`category_id`) REFERENCES `radiology_test_categories`(`id`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Radiology Test Orders
CREATE TABLE `radiology_test_orders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `visit_id` INT NOT NULL,
  `patient_id` INT NOT NULL,
  `test_id` INT NOT NULL,
  `ordered_by` INT NOT NULL COMMENT 'Doctor',
  `assigned_to` INT DEFAULT NULL COMMENT 'Radiologist',
  `priority` ENUM('normal','urgent','stat') DEFAULT 'normal',
  `status` ENUM('pending','scheduled','in_progress','completed','cancelled') DEFAULT 'pending',
  `clinical_notes` TEXT,
  `scheduled_datetime` DATETIME DEFAULT NULL,
  `cancellation_reason` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`visit_id`) REFERENCES `patient_visits`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`test_id`) REFERENCES `radiology_tests`(`id`),
  FOREIGN KEY (`ordered_by`) REFERENCES `users`(`id`),
  FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_patient` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Radiology Results
CREATE TABLE `radiology_results` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `order_id` INT NOT NULL,
  `patient_id` INT NOT NULL,
  `test_id` INT NOT NULL,
  `findings` TEXT,
  `impression` TEXT,
  `recommendations` TEXT,
  `images_path` VARCHAR(255),
  `is_normal` TINYINT(1) DEFAULT 1,
  `is_critical` TINYINT(1) DEFAULT 0,
  `radiologist_id` INT NOT NULL,
  `radiologist_notes` TEXT,
  `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `reviewed_by` INT DEFAULT NULL,
  `reviewed_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `radiology_test_orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`radiologist_id`) REFERENCES `users`(`id`),
  INDEX `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

### Task 1.3: Create IPD Tables
**File:** `database/migrations/003_create_ipd_tables.sql`

```sql
-- IPD Wards
CREATE TABLE `ipd_wards` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ward_name` VARCHAR(100) NOT NULL,
  `ward_code` VARCHAR(20) NOT NULL UNIQUE,
  `ward_type` ENUM('general','private','icu','maternity','pediatric','isolation') DEFAULT 'general',
  `total_beds` INT NOT NULL DEFAULT 0,
  `occupied_beds` INT NOT NULL DEFAULT 0,
  `floor_number` INT,
  `description` TEXT,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- IPD Beds
CREATE TABLE `ipd_beds` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ward_id` INT NOT NULL,
  `bed_number` VARCHAR(20) NOT NULL,
  `bed_type` ENUM('standard','oxygen','icu','isolation') DEFAULT 'standard',
  `status` ENUM('available','occupied','maintenance','reserved') DEFAULT 'available',
  `daily_rate` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `notes` TEXT,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`ward_id`) REFERENCES `ipd_wards`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_bed` (`ward_id`, `bed_number`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- IPD Admissions
CREATE TABLE `ipd_admissions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `patient_id` INT NOT NULL,
  `visit_id` INT NOT NULL,
  `bed_id` INT NOT NULL,
  `admission_number` VARCHAR(50) NOT NULL UNIQUE,
  `admission_datetime` DATETIME NOT NULL,
  `discharge_datetime` DATETIME DEFAULT NULL,
  `admission_type` ENUM('emergency','planned','transfer') DEFAULT 'planned',
  `admission_diagnosis` TEXT,
  `discharge_diagnosis` TEXT,
  `discharge_summary` TEXT,
  `admitted_by` INT NOT NULL,
  `attending_doctor` INT DEFAULT NULL,
  `discharged_by` INT DEFAULT NULL,
  `status` ENUM('active','discharged','transferred','deceased') DEFAULT 'active',
  `total_days` INT GENERATED ALWAYS AS (
    DATEDIFF(COALESCE(discharge_datetime, NOW()), admission_datetime)
  ) STORED,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`visit_id`) REFERENCES `patient_visits`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`bed_id`) REFERENCES `ipd_beds`(`id`),
  FOREIGN KEY (`admitted_by`) REFERENCES `users`(`id`),
  FOREIGN KEY (`attending_doctor`) REFERENCES `users`(`id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_patient` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- IPD Progress Notes
CREATE TABLE `ipd_progress_notes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `admission_id` INT NOT NULL,
  `patient_id` INT NOT NULL,
  `note_datetime` DATETIME NOT NULL,
  `note_type` ENUM('doctor','nurse','other') DEFAULT 'doctor',
  `temperature` DECIMAL(4,1),
  `blood_pressure_systolic` INT,
  `blood_pressure_diastolic` INT,
  `pulse_rate` INT,
  `respiratory_rate` INT,
  `oxygen_saturation` INT,
  `progress_note` TEXT,
  `recorded_by` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`admission_id`) REFERENCES `ipd_admissions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recorded_by`) REFERENCES `users`(`id`),
  INDEX `idx_admission` (`admission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- IPD Medication Administration
CREATE TABLE `ipd_medication_admin` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `admission_id` INT NOT NULL,
  `patient_id` INT NOT NULL,
  `medicine_id` INT NOT NULL,
  `prescribed_by` INT NOT NULL,
  `administered_by` INT DEFAULT NULL,
  `scheduled_datetime` DATETIME NOT NULL,
  `administered_datetime` DATETIME DEFAULT NULL,
  `dose` VARCHAR(100),
  `route` ENUM('oral','IV','IM','SC','topical','other') DEFAULT 'oral',
  `status` ENUM('scheduled','administered','missed','cancelled') DEFAULT 'scheduled',
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`admission_id`) REFERENCES `ipd_admissions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`),
  FOREIGN KEY (`prescribed_by`) REFERENCES `users`(`id`),
  FOREIGN KEY (`administered_by`) REFERENCES `users`(`id`),
  INDEX `idx_admission` (`admission_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

### Task 1.4: Seed Initial Data
**File:** `database/seeds/001_radiology_ipd_seed.sql`

```sql
-- Radiology Categories
INSERT INTO `radiology_test_categories` (`category_name`, `category_code`, `description`) VALUES
('X-Ray', 'XRAY', 'Radiography imaging'),
('Ultrasound', 'US', 'Ultrasound imaging'),
('CT Scan', 'CT', 'Computed Tomography'),
('MRI', 'MRI', 'Magnetic Resonance Imaging');

-- Sample Radiology Tests
INSERT INTO `radiology_tests` (`test_name`, `test_code`, `category_id`, `price`, `estimated_duration`) VALUES
('Chest X-Ray', 'XRAY-CHEST', 1, 30000.00, 15),
('Abdominal Ultrasound', 'US-ABD', 2, 40000.00, 30),
('Pelvic Ultrasound', 'US-PELV', 2, 40000.00, 30),
('Head CT Scan', 'CT-HEAD', 3, 150000.00, 20);

-- IPD Wards
INSERT INTO `ipd_wards` (`ward_name`, `ward_code`, `ward_type`, `total_beds`, `floor_number`) VALUES
('General Ward A', 'GEN-A', 'general', 20, 1),
('Private Ward', 'PRIV-1', 'private', 10, 2),
('ICU', 'ICU-1', 'icu', 6, 3),
('Maternity Ward', 'MAT-1', 'maternity', 15, 2);

-- Sample Beds (General Ward A)
INSERT INTO `ipd_beds` (`ward_id`, `bed_number`, `bed_type`, `daily_rate`) VALUES
(1, 'A-01', 'standard', 15000.00),
(1, 'A-02', 'standard', 15000.00),
(1, 'A-03', 'oxygen', 20000.00),
(1, 'A-04', 'standard', 15000.00),
(1, 'A-05', 'standard', 15000.00);

-- Private Ward Beds
INSERT INTO `ipd_beds` (`ward_id`, `bed_number`, `bed_type`, `daily_rate`) VALUES
(2, 'P-01', 'standard', 50000.00),
(2, 'P-02', 'standard', 50000.00);

-- ICU Beds
INSERT INTO `ipd_beds` (`ward_id`, `bed_number`, `bed_type`, `daily_rate`) VALUES
(3, 'ICU-01', 'icu', 150000.00),
(3, 'ICU-02', 'icu', 150000.00);

-- Add permissions for new roles
INSERT INTO `role_permissions` (`role`, `permission`) VALUES
('radiologist', 'dashboard.view'),
('radiologist', 'patients.view'),
('radiologist', 'radiology.view_orders'),
('radiologist', 'radiology.perform_test'),
('radiologist', 'radiology.record_result'),
('nurse', 'dashboard.view'),
('nurse', 'patients.view'),
('nurse', 'ipd.view'),
('nurse', 'ipd.record_vitals'),
('nurse', 'ipd.administer_medication'),
('nurse', 'ipd.progress_notes');
```

---

## PHASE 2: BACKEND CONTROLLERS (Priority: HIGH)

### Task 2.1: Create RadiologistController
**File:** `controllers/RadiologistController.php`

(See full implementation in attached file - 500+ lines)

**Key methods:**
- `dashboard()` - Show pending orders, today's schedule
- `orders()` - List orders by status (pending/in-progress/completed)
- `performTest($order_id)` - Start test execution
- `recordResult($order_id)` - Record findings, impression, upload images
- `viewResult($result_id)` - View completed result

---

### Task 2.2: Create IpdController (for Nurse + Receptionist)
**File:** `controllers/IpdController.php`

**Key methods:**
- `dashboard()` - Bed availability, active admissions stats
- `beds()` - Manage wards and beds
- `admit($visit_id)` - Admit patient to IPD
- `admissions()` - List active/discharged admissions
- `viewAdmission($admission_id)` - Full admission details
- `recordProgressNote($admission_id)` - Nurse/doctor notes
- `administerMedication($admission_id)` - Nurse medication admin
- `discharge($admission_id)` - Discharge patient

---

### Task 2.3: Update Existing Controllers

**DoctorController.php:**
- Add `orderRadiologyTest()` method
- Add `admitPatient()` method (redirect to IPD)
- Update `view_patient()` to show radiology orders

**ReceptionistController.php:**
- Already has multi-role support
- Update navigation to show IPD menu if user has nurse role

---

## PHASE 3: FRONTEND VIEWS (Priority: MEDIUM)

### Task 3.1: Radiologist Views
**Files to create:**
- `views/radiologist/dashboard.php`
- `views/radiologist/orders.php`
- `views/radiologist/perform_test.php`
- `views/radiologist/record_result.php`
- `views/radiologist/view_result.php`

### Task 3.2: IPD/Nurse Views
**Files to create:**
- `views/ipd/dashboard.php`
- `views/ipd/beds.php`
- `views/ipd/admit.php`
- `views/ipd/admissions.php`
- `views/ipd/view_admission.php`
- `views/ipd/progress_notes.php`
- `views/ipd/medication_admin.php`

### Task 3.3: Role Switcher UI
**File:** `views/layouts/main.php` (update header)

Add dropdown in top header:
```html
<!-- Role Switcher (if user has multiple roles) -->
<?php if (count($_SESSION['user_roles']) > 1): ?>
<div class="role-switcher">
    <select onchange="switchRole(this.value)">
        <?php foreach ($_SESSION['user_roles'] as $role): ?>
        <option value="<?php echo $role; ?>" 
                <?php echo ($_SESSION['active_role'] ?? $_SESSION['user_role']) === $role ? 'selected' : ''; ?>>
            <?php echo ucfirst($role); ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>
<?php endif; ?>
```

**Add switch role endpoint:**
```php
// In BaseController or AuthController
public function switchRole() {
    if (isset($_POST['role']) && $this->hasAnyRole([$_POST['role']])) {
        $_SESSION['active_role'] = $_POST['role'];
        $_SESSION['user_role'] = $_POST['role']; // Update primary session role
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid role']);
    }
}
```

---

## PHASE 4: ROUTING & NAVIGATION (Priority: MEDIUM)

### Task 4.1: Update Routing
**File:** `index.php`

Add routes:
```php
// Radiologist routes
if ($role === 'radiologist' && $controller === 'radiologist') {
    require_once 'controllers/RadiologistController.php';
    $ctrl = new RadiologistController();
    // ... route to methods
}

// IPD routes
if (in_array($role, ['nurse', 'receptionist', 'doctor', 'admin']) && $controller === 'ipd') {
    require_once 'controllers/IpdController.php';
    $ctrl = new IpdController();
    // ... route to methods
}
```

### Task 4.2: Update Sidebar Navigation
**File:** `views/layouts/main.php`

Add menu items based on active role:
```php
<?php if ($this->hasAnyRole(['radiologist'])): ?>
<!-- Radiologist Menu -->
<li><a href="/radiologist/dashboard">Radiology Dashboard</a></li>
<li><a href="/radiologist/orders">Test Orders</a></li>
<?php endif; ?>

<?php if ($this->hasAnyRole(['nurse', 'receptionist'])): ?>
<!-- IPD Menu -->
<li><a href="/ipd/dashboard">IPD Dashboard</a></li>
<li><a href="/ipd/admissions">Admissions</a></li>
<li><a href="/ipd/beds">Bed Management</a></li>
<?php endif; ?>
```

---

## PHASE 5: MULTI-ROLE USER MANAGEMENT (Priority: HIGH)

### Task 5.1: Create User Multi-Role Assignment UI
**File:** `views/admin/edit_user.php` (update)

Add checkbox for each role:
```html
<div class="roles-section">
    <h3>Assign Roles</h3>
    <?php foreach (['admin','receptionist','doctor','lab_technician','accountant','pharmacist','radiologist','nurse'] as $role): ?>
    <label>
        <input type="checkbox" name="roles[]" value="<?php echo $role; ?>"
               <?php echo in_array($role, $user_assigned_roles) ? 'checked' : ''; ?>>
        <?php echo ucfirst($role); ?>
    </label>
    <?php endforeach; ?>
    
    <label>
        <strong>Primary Role:</strong>
        <select name="primary_role">
            <?php foreach ($user_assigned_roles as $r): ?>
            <option value="<?php echo $r; ?>" <?php echo $user['role'] === $r ? 'selected' : ''; ?>>
                <?php echo ucfirst($r); ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>
</div>
```

### Task 5.2: Update AdminController->edit_user()
Handle multi-role assignment on save:
```php
// Delete old roles
$stmt = $pdo->prepare("DELETE FROM user_roles WHERE user_id = ?");
$stmt->execute([$user_id]);

// Insert new roles
$roles = $_POST['roles'] ?? [];
$primary = $_POST['primary_role'];

foreach ($roles as $role) {
    $stmt = $pdo->prepare("
        INSERT INTO user_roles (user_id, role, is_primary, granted_by)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $user_id,
        $role,
        ($role === $primary ? 1 : 0),
        $_SESSION['user_id']
    ]);
}

// Update primary role in users table
$stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
$stmt->execute([$primary, $user_id]);
```

---

## PHASE 6: TESTING & DEPLOYMENT (Priority: MEDIUM)

### Task 6.1: Create Test Users
```sql
-- Create test users with multiple roles
-- Example: Receptionist who is also a Nurse

-- Insert user
INSERT INTO users (username, email, first_name, last_name, role, password_hash, is_active)
VALUES ('nurse.jane', 'jane@hospital.com', 'Jane', 'Smith', 'receptionist', '$2y$10$...', 1);

-- Get last insert ID and assign multiple roles
SET @user_id = LAST_INSERT_ID();

INSERT INTO user_roles (user_id, role, is_primary, granted_by) VALUES
(@user_id, 'receptionist', 1, 1),
(@user_id, 'nurse', 0, 1);
```

### Task 6.2: Integration Tests
- [ ] User with receptionist+nurse roles can see both sidebars
- [ ] Role switcher changes active role and updates menu
- [ ] Radiologist can view orders and record results
- [ ] Nurse can admit patient, record vitals, administer medication
- [ ] Payment flow includes radiology tests
- [ ] IPD admission creates visit_type='ipd' record

---

## PRIORITY EXECUTION ORDER

**Week 1 (Critical):**
1. ✅ Run migration 001 (add roles to ENUM) - 5 min
2. ✅ Run migration 002 (radiology tables) - 10 min
3. ✅ Run migration 003 (IPD tables) - 15 min
4. ✅ Run seed data - 5 min
5. ✅ Create RadiologistController - 2 hours
6. ✅ Create IpdController - 3 hours
7. ✅ Update routing (index.php) - 30 min

**Week 2 (High Priority):**
8. Create radiologist views (5 files) - 4 hours
9. Create IPD views (7 files) - 6 hours
10. Add role switcher UI - 1 hour
11. Update admin user management for multi-role - 2 hours

**Week 3 (Medium Priority):**
12. Test multi-role functionality - 2 hours
13. Update existing controllers (Doctor, Receptionist) - 2 hours
14. Update sidebar navigation - 1 hour
15. Create test users and demo data - 1 hour

**Week 4 (Polish):**
16. Add payment integration for radiology - 2 hours
17. Add reports for IPD statistics - 2 hours
18. Documentation and training materials - 2 hours
19. Production deployment - 1 hour

---

## QUICK START COMMANDS

```bash
# 1. Run all migrations
mysql -u root -p zahanati < database/migrations/001_add_nurse_radiologist_roles.sql
mysql -u root -p zahanati < database/migrations/002_create_radiology_tables.sql
mysql -u root -p zahanati < database/migrations/003_create_ipd_tables.sql
mysql -u root -p zahanati < database/seeds/001_radiology_ipd_seed.sql

# 2. Verify tables created
mysql -u root -p zahanati -e "SHOW TABLES LIKE '%radiology%'; SHOW TABLES LIKE '%ipd%';"

# 3. Create test multi-role user
mysql -u root -p zahanati -e "
INSERT INTO users (username, email, first_name, last_name, role, password_hash, is_active) 
VALUES ('nurse.test', 'nurse@test.com', 'Test', 'Nurse', 'receptionist', '\$2y\$10\$abcdef', 1);
SET @uid = LAST_INSERT_ID();
INSERT INTO user_roles (user_id, role, is_primary, granted_by) VALUES (@uid, 'receptionist', 1, 1), (@uid, 'nurse', 0, 1);
"

# 4. Check multi-role works
mysql -u root -p zahanati -e "SELECT u.username, ur.role, ur.is_primary FROM users u JOIN user_roles ur ON u.id=ur.user_id WHERE u.username='nurse.test';"
```

---

## NOTES FOR DEVELOPER

1. **Your system already has multi-role infrastructure!** This is 80% of the work. You just need to:
   - Add radiologist/nurse to ENUMs
   - Build the controllers and views
   - Update navigation

2. **Role switching:** Users can switch between roles using the dropdown. Update `$_SESSION['active_role']` and reload.

3. **Permissions:** The `role_permissions` table controls fine-grained access. Use `BaseController->hasPermission('radiology.perform_test')` for granular checks.

4. **Your dispensary context:** Since receptionist is also nurse, create users with both roles. No code changes needed beyond adding the role to user_roles table.

5. **Testing:** After running migrations, log in as admin and go to Users → Edit → assign multiple roles. Then log in as that user and test role switcher.

---

## FILES TO CREATE (CHECKLIST)

### Migrations (SQL)
- [ ] `database/migrations/001_add_nurse_radiologist_roles.sql`
- [ ] `database/migrations/002_create_radiology_tables.sql`
- [ ] `database/migrations/003_create_ipd_tables.sql`
- [ ] `database/seeds/001_radiology_ipd_seed.sql`

### Controllers (PHP)
- [ ] `controllers/RadiologistController.php`
- [ ] `controllers/IpdController.php`

### Views - Radiologist (PHP)
- [ ] `views/radiologist/dashboard.php`
- [ ] `views/radiologist/orders.php`
- [ ] `views/radiologist/perform_test.php`
- [ ] `views/radiologist/record_result.php`
- [ ] `views/radiologist/view_result.php`

### Views - IPD (PHP)
- [ ] `views/ipd/dashboard.php`
- [ ] `views/ipd/beds.php`
- [ ] `views/ipd/admit.php`
- [ ] `views/ipd/admissions.php`
- [ ] `views/ipd/view_admission.php`
- [ ] `views/ipd/progress_notes.php`
- [ ] `views/ipd/medication_admin.php`

### Updates to Existing Files
- [ ] `views/layouts/main.php` - Add role switcher and IPD/Radiology menu
- [ ] `index.php` - Add routing for radiologist/ipd controllers
- [ ] `views/admin/edit_user.php` - Add multi-role checkboxes
- [ ] `controllers/AdminController.php` - Update edit_user() to save multiple roles
- [ ] `controllers/DoctorController.php` - Add orderRadiologyTest(), admitPatient()

---

**END OF IMPLEMENTATION ROADMAP**
