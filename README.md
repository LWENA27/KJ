# KJ - Dispensary Management System

A comprehensive PHP-based healthcare dispensary management system for patient registration, consultations, lab testing, and medicine dispensing.

## Features

- **Patient Management**: Registration, visit tracking, medical history
- **Doctor Workflows**: Consultations, prescriptions, lab orders
- **Lab Management**: Test orders, sample collection, results entry
- **Pharmacy**: Medicine inventory (batch tracking), prescription dispensing
- **Receptionist**: Patient registration, payments, medicine dispensing
- **Admin**: User management, medicine/test catalog, reports

## Tech Stack

- **Backend**: PHP 7.4+ with PDO
- **Database**: MySQL/MariaDB (visit-centric normalized schema)
- **Frontend**: Tailwind CSS, vanilla JavaScript
- **Server**: Apache/XAMPP

## Installation

1. **Clone Repository**
   ```bash
   git clone https://github.com/LWENA27/KJ.git
   cd KJ
   ```

2. **Database Setup**
   - Create database: `CREATE DATABASE zahanati;`
   - Import schema: `mysql -u root zahanati < database/zahanati.sql`
   - See `database/IMPORT_INSTRUCTIONS.md` for detailed steps

3. **Configuration**
   - Edit `config/database.php` with your database credentials
   - Ensure `tmp/sessions/` directory is writable

4. **Access Application**
   - URL: `http://localhost/KJ/`
   - Default credentials in `database/IMPORT_INSTRUCTIONS.md`

## Project Structure

```
KJ/
├── assets/          # CSS, icons, webfonts
├── config/          # Database configuration
├── controllers/     # Business logic (MVC controllers)
├── database/        # Schema, migrations, import guide
├── includes/        # Helpers, BaseController, logger
├── logs/            # Application logs
├── tmp/sessions/    # PHP session storage
├── views/           # UI templates (admin, doctor, lab, receptionist)
└── index.php        # Application entry point
```

## Documentation

- **[COMPATIBILITY_FIXES.md](COMPATIBILITY_FIXES.md)** - Recent database compatibility changes
- **[database/IMPORT_INSTRUCTIONS.md](database/IMPORT_INSTRUCTIONS.md)** - Database setup guide
- **[database/zahanati.sql](database/zahanati.sql)** - Complete schema with demo data

## Demo Accounts

After importing the database:

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | password |
| Receptionist | reception | password |
| Doctor | doctor | password |
| Lab Technician | lab | password |

## Workflow

1. **Registration** (Receptionist): Register patient → Create visit → Collect payment
2. **Consultation** (Doctor): Review patient → Diagnose → Prescribe medicine/tests
3. **Lab Tests** (Lab Tech): Collect samples → Process tests → Enter results
4. **Dispensing** (Receptionist): Verify payment → Dispense medicine (FEFO)

## Key Features

- **Visit-Centric Design**: All workflows revolve around `patient_visits`
- **Batch Tracking**: Medicine stock tracked by batch with expiry dates
- **FEFO Dispensing**: First-Expiry-First-Out automatic batch selection
- **Payment Tracking**: Linked to visits (registration, consultation, lab, medicine)
- **Workflow Status**: Auto-derived from visit status + payments + consultations

## Development

- **Tailwind CSS**: Run `npm install` then `npx tailwindcss -i assets/css/input.css -o assets/css/tailwind.css --watch`
- **Logs**: Check `logs/` directory for errors and debugging
- **PHP Linting**: Use `php tools/php_lint.php <file>` to validate syntax

## License

ISC

## Repository

GitHub: [LWENA27/KJ](https://github.com/LWENA27/KJ)
