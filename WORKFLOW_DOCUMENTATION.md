# Healthcare Management System - Workflow Documentation

## Overview

## Workflow Process

### 1. Patient Registration & Consultation (Receptionist)
**User Role:** Receptionist
**Actions:**
- Register new patient with basic information
- Collect combined consultation/registration fee
**Required Information:**
- Personal details (name, DOB, gender, contact)
- Emergency contact information
**Payment Required:** Yes (Consultation Fee)
**Next Step Unlocks:** Lab Tests

### 2. Lab Tests (Lab Technician)

**User Role:** Lab Technician
**Prerequisites:**
- Patient must be registered and consultation fee paid

**Actions:**
- Receive test orders from doctor
- Collect lab test payment
**Payment Required:** Yes (Lab Test Fee)
**Next Step Unlocks:** Results Review

### 3. Results Review (Doctor)

**User Role:** Doctor
**Prerequisites:**
- Lab tests completed
- Lab test payment completed
**Actions:**
- Review lab test results
- Update patient diagnosis if needed
**Payment Required:** No (Included in consultation fee)
**Process Completion:** Patient treatment cycle complete

## Payment System

### Payment Methods Supported
- Cash
- Card
- Mobile Money
- Insurance

### Fee Structure (Example)

- Registration Fee: TZS 10,000
- Consultation Fee: TZS 25,000
- Lab Test Fee: TZS 15,000 (per test category)
- Results Review Fee: TZS 5,000

### Payment Tracking

- Each step payment is recorded separately
- Payment history maintained per patient
- Automatic workflow progression upon payment
- Refund capability for failed/cancelled services

## Database Schema

### Core Tables
- `patients` - Patient demographic information
- `workflow_status` - Tracks patient progress through workflow
- `step_payments` - Individual payment records per workflow step
- `users` - System users (admin, receptionist, doctor, lab technician)
- `consultations` - Doctor consultation records
- `lab_results` - Laboratory test results

### Workflow Status Fields

- `current_step` - Current workflow position
- `registration_paid` - Boolean flag for registration payment
- `consultation_paid` - Boolean flag for consultation payment
- `lab_tests_paid` - Boolean flag for lab test payment
- `results_review_paid` - Boolean flag for results review payment

## User Role Permissions

### Receptionist
- ✅ Register new patients
- ✅ Process registration payments
- ✅ View patient list with workflow status
- ✅ Manage appointments
- ❌ Access patient medical records
- ❌ Modify doctor consultations

### Doctor
- ✅ View patients (after registration payment)
- ✅ Conduct consultations
- ✅ Order lab tests
- ✅ Review lab results (after lab payment)
- ✅ Process consultation payments
- ❌ Register new patients
- ❌ Perform lab tests

### Lab Technician
- ✅ View test orders (after consultation payment)
- ✅ Perform lab tests
- ✅ Record test results
- ✅ Process lab test payments
- ❌ Register patients
- ❌ Conduct consultations
- ❌ Review final results

### Admin
- ✅ Full system access
- ✅ User management
- ✅ System configuration
- ✅ Payment reports
- ✅ Workflow monitoring

## Security Features

### Access Control
- Role-based authentication
- Step-by-step information access
- Payment verification before data access
- Session management with CSRF protection

### Data Protection

- Input sanitization
- SQL injection prevention
- XSS protection
- Encrypted password storage

## API Endpoints

### Receptionist Endpoints
- `POST /receptionist/register_patient` - Register new patient
- `GET /receptionist/patients` - List all patients
- `GET /receptionist/appointments` - View appointments
- `GET /receptionist/payments` - View payments

### Doctor Endpoints

- `GET /doctor/patients` - View accessible patients
- `POST /doctor/process_consultation_payment` - Process payment
- `GET /doctor/consultations` - View consultations
- `GET /doctor/lab_results` - View lab results

### Lab Technician Endpoints

- `GET /lab/tests` - View pending tests
- `POST /lab/process_lab_payment` - Process lab payment
- `GET /lab/results` - Record test results

## Workflow Monitoring

### Dashboard Features
- Real-time workflow status tracking
- Payment status indicators
- Step completion statistics
- Patient progress visualization

### Reporting

- Payment reports by date/range
- Workflow completion rates
- User activity logs
- Revenue tracking

## Implementation Notes

### Payment Integration
- Modular payment system
- Support for multiple payment gateways
- Offline payment recording
- Payment verification workflow

### Workflow Flexibility

- Configurable workflow steps
- Dynamic fee management
- Customizable user roles
- Extensible step system

### Performance Optimization

- Database indexing on workflow fields
- Caching for frequently accessed data
- Optimized queries for large datasets
- Background processing for reports

## Future Enhancements

### Planned Features
- SMS notifications for payment/status updates
- Mobile app for patient self-service
- Integration with external lab systems
- Advanced reporting and analytics
- Multi-language support
- Automated appointment reminders

### Scalability Considerations

- Database sharding for large deployments
- Microservices architecture option
- Cloud deployment support
- API rate limiting and optimization

## Troubleshooting

### Common Issues
1. **Payment Not Processing**
   - Check payment method configuration
   - Verify user permissions
   - Check database connection

2. **Workflow Not Progressing**
   - Ensure all required fields are completed
   - Verify payment status
   - Check user role permissions

3. **Access Denied Errors**
   - Confirm user authentication
   - Check workflow status
   - Verify payment completion

## Support and Maintenance

### Regular Tasks
- Database backup scheduling
- Payment gateway monitoring
- User access log review
- Performance optimization

### Monitoring

- System health checks
- Payment processing alerts
# Healthcare Management System - Workflow Documentation

## Overview
The Healthcare Management System implements a comprehensive workflow-based patient management system where information flows sequentially from one user role to another, with payment required at each step to access subsequent information.

## Workflow Process

### 1. Patient Registration & Consultation (Receptionist)
**User Role:** Receptionist
**Actions:**
- Register new patient with basic information
- Collect combined consultation/registration fee
- Initialize patient workflow

**Required Information:**
- Personal details (name, DOB, gender, contact)
- Emergency contact information
- Occupation and address
- Combined consultation/registration fee payment

**Payment Required:** Yes (Consultation Fee)
**Next Step Unlocks:** Lab Tests

### 2. Lab Tests (Lab Technician)
**User Role:** Lab Technician
**Prerequisites:**
- Patient must be registered and consultation fee paid

**Actions:**
- Receive test orders from doctor
- Collect lab test payment
- Perform laboratory tests
- Record test results

**Payment Required:** Yes (Lab Test Fee)
**Next Step Unlocks:** Results Review

### 3. Results Review (Doctor)
**User Role:** Doctor
**Prerequisites:**
- Lab tests completed
- Lab test payment completed

**Actions:**
- Review lab test results
- Update patient diagnosis if needed
- Finalize treatment plan

**Payment Required:** No (Included in consultation fee)
**Process Completion:** Patient treatment cycle complete

## Payment System

### Payment Methods Supported
- Cash
- Card
- Mobile Money
- Insurance

### Fee Structure (Example)
- Registration Fee: TZS 10,000
- Consultation Fee: TZS 25,000
- Lab Test Fee: TZS 15,000 (per test category)
- Results Review Fee: TZS 5,000

### Payment Tracking
- Each step payment is recorded separately
- Payment history maintained per patient
- Automatic workflow progression upon payment
- Refund capability for failed/cancelled services

## Database Schema

### Core Tables
- `patients` - Patient demographic information
- `workflow_status` - Tracks patient progress through workflow
- `step_payments` - Individual payment records per workflow step
- `users` - System users (admin, receptionist, doctor, lab technician)
- `consultations` - Doctor consultation records
- `lab_results` - Laboratory test results

### Workflow Status Fields
- `current_step` - Current workflow position
- `registration_paid` - Boolean flag for registration payment
- `consultation_paid` - Boolean flag for consultation payment
- `lab_tests_paid` - Boolean flag for lab test payment
- `results_review_paid` - Boolean flag for results review payment

## User Role Permissions

### Receptionist
- ✅ Register new patients
- ✅ Process registration payments
- ✅ View patient list with workflow status
- ✅ Manage appointments
- ❌ Access patient medical records
- ❌ Modify doctor consultations

### Doctor
- ✅ View patients (after registration payment)
- ✅ Conduct consultations
- ✅ Order lab tests
- ✅ Review lab results (after lab payment)
- ✅ Process consultation payments
- ❌ Register new patients
- ❌ Perform lab tests

### Lab Technician
- ✅ View test orders (after consultation payment)
- ✅ Perform lab tests
- ✅ Record test results
- ✅ Process lab test payments
- ❌ Register patients
- ❌ Conduct consultations
- ❌ Review final results

### Admin
- ✅ Full system access
- ✅ User management
- ✅ System configuration
- ✅ Payment reports
- ✅ Workflow monitoring

## Security Features

### Access Control
- Role-based authentication
- Step-by-step information access
- Payment verification before data access
- Session management with CSRF protection

### Data Protection
- Input sanitization
- SQL injection prevention
- XSS protection
- Encrypted password storage

## API Endpoints

### Receptionist Endpoints
- `POST /receptionist/register_patient` - Register new patient
- `GET /receptionist/patients` - List all patients
- `GET /receptionist/appointments` - View appointments
- `GET /receptionist/payments` - View payments

### Doctor Endpoints
- `GET /doctor/patients` - View accessible patients
- `POST /doctor/process_consultation_payment` - Process payment
- `GET /doctor/consultations` - View consultations
- `GET /doctor/lab_results` - View lab results

### Lab Technician Endpoints
- `GET /lab/tests` - View pending tests
- `POST /lab/process_lab_payment` - Process lab payment
- `GET /lab/results` - Record test results

## Workflow Monitoring

### Dashboard Features
- Real-time workflow status tracking
- Payment status indicators
- Step completion statistics
- Patient progress visualization

### Reporting
- Payment reports by date/range
- Workflow completion rates
- User activity logs
- Revenue tracking

## Implementation Notes

### Payment Integration
- Modular payment system
- Support for multiple payment gateways
- Offline payment recording
- Payment verification workflow

### Workflow Flexibility
- Configurable workflow steps
- Dynamic fee management
- Customizable user roles
- Extensible step system

### Performance Optimization
- Database indexing on workflow fields
- Caching for frequently accessed data
- Optimized queries for large datasets
- Background processing for reports

## Future Enhancements

### Planned Features
- SMS notifications for payment/status updates
- Mobile app for patient self-service
- Integration with external lab systems
- Advanced reporting and analytics
- Multi-language support
- Automated appointment reminders

### Scalability Considerations
- Database sharding for large deployments
- Microservices architecture option
- Cloud deployment support
- API rate limiting and optimization

## Troubleshooting

### Common Issues
1. **Payment Not Processing**
   - Check payment method configuration
   - Verify user permissions
   - Check database connection

2. **Workflow Not Progressing**
   - Ensure all required fields are completed
   - Verify payment status
   - Check user role permissions

3. **Access Denied Errors**
   - Confirm user authentication
   - Check workflow status
   - Verify payment completion

## Support and Maintenance

### Regular Tasks
- Database backup scheduling
- Payment gateway monitoring
- User access log review
- Performance optimization

### Monitoring
- System health checks
- Payment processing alerts
- Workflow bottleneck identification
- User activity monitoring

- Workflow bottleneck identification
- User activity monitoring


**Version:** 1.0.0
**Last Updated:** August 31, 2025
**Contact:** System Administrator
