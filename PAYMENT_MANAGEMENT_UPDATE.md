# Payment Management System Update

## Overview
Enhanced the receptionist payments page to display and process pending payments for lab tests and medicines that have been ordered/prescribed but not yet paid for.

## Changes Made

### 1. Controller Updates (`controllers/ReceptionistController.php`)

#### Modified `payments()` Method
- **Added pending lab test payments query**: Fetches all lab test orders that don't have corresponding paid payment records
- **Added pending medicine payments query**: Fetches all prescriptions that don't have corresponding paid payment records
- **Improved sorting**: Orders payments by status (pending first) then by creation date
- Both queries include:
  - Patient information (name, registration number)
  - Visit information (visit_id, visit_date)
  - Item details (test names or medicine names)
  - Total amount calculated from prices
  - Creation timestamp

#### Added `record_payment()` Method
- **Purpose**: Records payments for pending lab tests or medicines
- **Validates**: Patient ID, visit ID, payment type, and amount
- **Prevents duplicates**: Checks if payment already exists before inserting
- **Creates payment record**: Inserts into payments table with proper linking
- **Updates workflow**: Automatically updates patient workflow status based on payment type
  - Lab test payments → Updates to 'lab_testing' status
  - Medicine payments → Updates to 'medicine_dispensing' status
- **Transaction safety**: Uses database transactions to ensure data integrity

### 2. View Updates (`views/receptionist/payments.php`)

#### Enhanced Statistics Cards
- **Added 5th card**: "Pending Payments" showing count of unpaid lab tests and medicines
- **Color coding**: Red card with exclamation icon to draw attention

#### New Pending Payments Section
- **Visual alert**: Red bordered section at top of page for pending payments
- **Separate tables for**:
  1. **Pending Lab Test Payments**
     - Shows patient name and registration number
     - Lists all ordered tests
     - Displays total amount
     - Shows order date
     - "Record Payment" button (red theme)
  
  2. **Pending Medicine Payments**
     - Shows patient name and registration number
     - Lists all prescribed medicines
     - Displays total amount
     - Shows prescription date
     - "Record Payment" button (orange theme)

#### Payment Recording Modal
- **Professional design**: Modal overlay with gradient header
- **Fields**:
  - Patient name (read-only display)
  - Payment type (read-only display)
  - Amount (read-only, pre-filled)
  - Payment method (dropdown: cash, card, mobile money, insurance)
  - Reference number (optional)
- **Actions**:
  - Cancel button (closes modal)
  - Record Payment button (submits form)
- **User experience**:
  - Can be closed with Escape key
  - Can be closed by clicking outside
  - Form validation

#### JavaScript Enhancements
- **`openPaymentModal()` function**: Opens modal with pre-filled patient and payment data
- **`closePaymentModal()` function**: Closes modal and resets form
- **Event listeners**: Keyboard shortcuts and click-outside-to-close functionality

## Database Schema Used

### Tables Queried:
1. **`payments`**: Main payment records
2. **`lab_test_orders`**: Lab test orders to identify unpaid tests
3. **`prescriptions`**: Medicine prescriptions to identify unpaid medicines
4. **`patients`**: Patient information
5. **`patient_visits`**: Visit information for payment linking
6. **`lab_tests`**: Test details and prices
7. **`medicines`**: Medicine details and prices
8. **`consultations`**: Consultation information

### Key Relationships:
- Payments are linked to visits via `visit_id`
- Lab test orders are linked to visits
- Prescriptions are linked to consultations, which are linked to visits
- Pending payments identified by LEFT JOIN with payments table where payment is NULL

## Workflow Integration

### Payment Status Tracking
- **Before payment**: Lab tests and medicines show as pending in the system
- **After payment**: 
  - Payment record created with status 'paid'
  - Patient workflow status updated automatically
  - Items ready for next stage (lab processing or medicine dispensing)

### Receptionist Workflow
1. Navigate to Payments page
2. See pending payments highlighted at top
3. Click "Record Payment" on any pending item
4. Select payment method and add reference if needed
5. Submit payment
6. System automatically updates patient status
7. Item no longer appears in pending payments

## Security Features
- **CSRF protection**: All forms include CSRF token validation
- **Input validation**: Amount, patient ID, and visit ID validated
- **Duplicate prevention**: Checks for existing payments before creating new ones
- **Transaction safety**: Database rollback on errors
- **Role-based access**: Only receptionists can access this functionality

## User Interface Features
- **Color coding**:
  - Red: Lab test pending payments (urgent attention)
  - Orange: Medicine pending payments
  - Purple: Main payment interface theme
- **Responsive design**: Works on all screen sizes
- **Hover effects**: Cards and buttons provide visual feedback
- **Loading states**: Buttons show loading when clicked
- **Icons**: Font Awesome icons for visual clarity
- **TSH currency**: All amounts displayed in Tanzania Shillings format

## Benefits
1. **Visibility**: Receptionist can immediately see what payments are pending
2. **Efficiency**: Quick access to record payments without searching
3. **Accuracy**: Pre-filled amounts prevent errors
4. **Tracking**: Complete audit trail of all payments
5. **Workflow**: Automatic status updates keep system synchronized
6. **Patient Experience**: Faster processing reduces wait times

## Testing Recommendations
1. Test recording lab test payment
2. Test recording medicine payment
3. Verify duplicate payment prevention
4. Check workflow status updates
5. Test with multiple pending payments
6. Verify payment appears in history after recording
7. Test all payment methods
8. Verify optional reference number field
9. Test modal close functionality
10. Check responsive design on mobile

## Future Enhancements (Optional)
- Add partial payment support
- Add payment receipts generation
- Add payment history filtering
- Add payment reports by date range
- Add payment method statistics
- Add discount/waiver functionality
- Add payment reminder notifications
- Add bulk payment processing

---
**Date**: October 11, 2025
**Modified Files**: 
- `controllers/ReceptionistController.php`
- `views/receptionist/payments.php`
