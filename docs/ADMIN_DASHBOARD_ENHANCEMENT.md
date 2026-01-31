# Admin Dashboard Enhancement Documentation

## Overview
This document describes the enhancements made to the admin dashboard to provide comprehensive, real-time oversight of hospital operations with special emphasis on dispensary income tracking.

## Changes Made

### 1. Controller Updates (`controllers/AdminController.php`)

#### Updated `dashboard()` Method
- Added call to new `getRecentActivity()` method
- Now passes both statistics and recent activity to the view

#### Enhanced `getDashboardStats()` Method
Added comprehensive metrics across all hospital operations:

##### Financial Metrics
- **Dispensary Income** (specifically requested by admin)
  - `dispensary_income_today`: Today's pharmacy income from prescription payments
  - `dispensary_income_month`: Current month's pharmacy income
  - `dispensary_income_total`: All-time total pharmacy income
  
- **Overall Revenue**
  - `revenue_today`: Total revenue across all services today
  - `revenue_month`: Total revenue for current month
  
- **Revenue Breakdown by Service Type** (This Month)
  - `revenue_consultation`: Consultation fees
  - `revenue_pharmacy`: Pharmacy/dispensary income
  - `revenue_lab`: Laboratory test income
  - `revenue_radiology`: Radiology service income
  - `revenue_ipd`: In-patient department services

- **Payment Statistics**
  - `completed_payments`: Total count of completed payments

##### Operational Metrics

**Appointments**
- `appointments_today`: Count of patient visits today
- `pending_appointments`: Visits without completed consultations

**Lab Department**
- `pending_lab_orders`: Tests in pending/in-progress status
- `lab_completed_today`: Lab results completed today

**Radiology Department**
- `pending_radiology_orders`: Pending radiology orders
- `radiology_completed_today`: Radiology tests completed today

**Pharmacy Department**
- `pending_prescriptions`: Prescriptions awaiting dispensing
- `dispensed_today`: Prescriptions dispensed today

**IPD (In-Patient Department)**
- `active_admissions`: Currently admitted patients
- `admissions_today`: New admissions today
- `total_beds`: Total active beds in the system
- `available_beds`: Available beds
- `occupied_beds`: Currently occupied beds
- `bed_occupancy_percent`: Calculated occupancy percentage

#### New `getRecentActivity()` Method
Queries and combines real-time activity from multiple tables:

- **Patient Registrations**: Recent new patient registrations
- **Consultations**: Completed consultations with doctor and patient names
- **Stock Updates**: Medicine stock additions from `medicine_batches`
- **Payments**: Recent completed payment transactions

Activities are:
- Combined from all sources
- Sorted by timestamp (most recent first)
- Limited to 10 most recent items
- Include formatted descriptions and timestamps

### 2. View Updates (`views/admin/dashboard.php`)

Complete redesign with organized sections:

#### Section 1: Financial Overview 💰
Displays 8 financial cards:
- Dispensary income (today, this month)
- Total revenue (today, this month)
- Completed payments count
- Revenue breakdown by service (Consultation, Lab, Radiology)

**Visual Design:**
- Emerald/green colors for dispensary metrics (primary focus)
- Green colors for general revenue
- Blue for payments
- Service-specific colors (indigo, purple, pink) for breakdowns

#### Section 2: Patient Care 🏥
Shows patient-related metrics:
- Total patients
- Today's consultations
- Today's appointments (visits)
- Pending appointments

**Visual Design:**
- Green, yellow, blue, orange color scheme
- Icons: user-injured, user-md, calendar-day, clock

#### Section 3: Departments 🏢

**Pharmacy 💊**
- Total medicines
- Pending prescriptions
- Dispensed today
- Total dispensary income (all-time)

**Laboratory 🔬**
- Pending lab orders
- Tests completed today
- Lab revenue (this month)

**Radiology 🩻**
- Pending radiology orders
- Tests completed today
- Radiology revenue (this month)

**IPD 🛏️**
- Active admissions
- New admissions today
- Bed statistics (occupied/total)
- Bed occupancy percentage

#### Section 4: Alerts ⚠️
Dynamic alert cards that only show when conditions are met:
- Low stock medicines (red alert)
- Pending payments (yellow alert)
- Pending lab tests (purple alert)
- Pending prescriptions (orange alert)
- Pending radiology tests (pink alert)
- High bed occupancy (red alert, when ≥90%)

#### Section 5: System Info 👥
- Total users
- Total completed payments

#### Section 6: Recent Activity 📋
Real-time activity feed showing:
- Patient registrations (green dot)
- Consultations (blue dot)
- Stock updates (purple dot)
- Payments (emerald dot)

Each activity displays:
- Color-coded indicator
- Descriptive message
- Relative timestamp ("2 hours ago", "just now", etc.)

## Technical Details

### Database Queries
All queries follow these patterns:
- Use `COALESCE()` to handle NULL values
- Use prepared statements for date filtering
- Use `DATE()` function for today's metrics: `DATE(column) = CURDATE()`
- Use `MONTH()` and `YEAR()` for monthly metrics
- Handle division by zero in percentage calculations

### Currency Formatting
All monetary values displayed as: `Tsh [amount formatted with thousand separators]`
Example: `Tsh 150,000`

The system includes a `format_tsh()` helper function in `includes/helpers.php` for consistent formatting.

### Color Scheme
Consistent Tailwind CSS color classes:
- **Emerald/Green**: Primary financial metrics (dispensary, revenue)
- **Blue**: System/users/completed items
- **Yellow**: Consultations, warnings
- **Purple**: Medicine/pharmacy operations
- **Orange**: Pending items, time-sensitive
- **Pink**: Radiology services
- **Red**: Critical alerts
- **Teal**: Percentages/statistics

### Icons
Font Awesome 5 icons used throughout:
- `fa-prescription-bottle`: Dispensary
- `fa-dollar-sign`: Revenue
- `fa-user-injured`: Patients
- `fa-user-md`: Consultations
- `fa-bed`: IPD/Beds
- `fa-flask`: Laboratory
- `fa-x-ray`: Radiology
- `fa-pills`: Pharmacy
- `fa-exclamation-triangle`: Alerts

## Responsive Design
The dashboard uses Tailwind's responsive grid system:
- Mobile: 1 column (`grid-cols-1`)
- Tablet: 2 columns (`md:grid-cols-2`)
- Desktop: 4 columns (`lg:grid-cols-4`)

Sections adjust appropriately for different screen sizes.

## Performance Considerations
- All queries use indexes on date columns
- Separate queries rather than complex JOINs for better readability and performance
- Recent activity limited to 10 items to keep payload small
- Counts use optimized COUNT(*) queries

## Security
- All user inputs are sanitized
- No SQL injection vulnerabilities (using prepared statements)
- Output is HTML-escaped using `htmlspecialchars()`
- CSRF protection maintained through existing framework

## Future Enhancements
Potential improvements for future iterations:
1. Add date range filters for financial metrics
2. Add graphs/charts for revenue trends
3. Real-time updates using AJAX/websockets
4. Export functionality for reports
5. Drill-down capability from metrics to detailed views
6. Comparison with previous periods (MTD, YTD)
7. Performance indicators and goals tracking

## Testing Checklist
- [ ] Verify all metrics display correctly with real data
- [ ] Test with zero values (no data scenarios)
- [ ] Test alerts show/hide correctly based on conditions
- [ ] Verify recent activity updates with new transactions
- [ ] Test responsive design on mobile/tablet/desktop
- [ ] Verify currency formatting is consistent
- [ ] Check for PHP errors in error logs
- [ ] Test bed occupancy percentage calculation
- [ ] Verify date filters work correctly (today, this month)
- [ ] Test with different user roles to ensure proper access control
