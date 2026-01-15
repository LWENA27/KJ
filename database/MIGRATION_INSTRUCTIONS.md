# Database Migration Instructions

## ICD Diagnosis Codes Migration (NMCP Compliance)

This migration adds standardized ICD-10 diagnosis codes to comply with Tanzania NMCP guidelines.

### Reference
- NMCP Guidelines: https://www.nmcp.go.tz/storage/app/uploads/public/643/91b/120/64391b120fef4281592461.pdf

### What This Migration Does

1. **Creates `icd_codes` table** - Reference table for standardized diagnosis codes (ICD-10)
2. **Adds foreign key columns to `consultations` table**:
   - `preliminary_diagnosis_id` - Links to ICD code
   - `final_diagnosis_id` - Links to ICD code
3. **Populates common diagnoses** - Includes 70+ common diagnoses for Tanzania:
   - Malaria (B50-B54) - NMCP priority
   - Respiratory infections (J00-J45)
   - Gastrointestinal diseases (A00-A09)
   - HIV/AIDS and TB (A15-B24)
   - Other common conditions

### How to Apply the Migration

#### Option 1: Using MySQL Command Line
```bash
mysql -u root -p zahanati < database/add_icd_diagnosis_codes.sql
```

#### Option 2: Using phpMyAdmin
1. Open phpMyAdmin
2. Select the `zahanati` database
3. Go to "Import" tab
4. Choose file: `database/add_icd_diagnosis_codes.sql`
5. Click "Go"

#### Option 3: Using MySQL Workbench
1. Open MySQL Workbench
2. Connect to your database server
3. File → Run SQL Script
4. Select: `database/add_icd_diagnosis_codes.sql`
5. Execute

### Verification

After running the migration, verify it was successful:

```sql
-- Check if icd_codes table was created
SHOW TABLES LIKE 'icd_codes';

-- Count diagnosis codes
SELECT COUNT(*) FROM icd_codes;
-- Should return approximately 70 rows

-- Check if foreign key columns were added
DESCRIBE consultations;
-- Should show preliminary_diagnosis_id and final_diagnosis_id columns

-- View some sample diagnoses
SELECT code, name, category FROM icd_codes LIMIT 10;
```

### Backward Compatibility

The migration maintains backward compatibility:
- Old text fields (`preliminary_diagnosis`, `final_diagnosis`) are **preserved**
- When a diagnosis is selected from ICD codes, the text field is also populated with the diagnosis name
- Existing consultations will continue to work with text-based diagnoses
- New consultations will use ICD codes but also populate text fields

### Post-Migration Steps

1. **Clear application cache** (if any):
   ```bash
   rm -rf logs/*.log
   ```

2. **Test the diagnosis search**:
   - Login as a doctor
   - Go to Patients → Attend Patient
   - Try searching for "Malaria" in the diagnosis field
   - Verify dropdown shows ICD codes

3. **Add more diagnosis codes** (optional):
   - Use the admin panel (when available) to add more ICD-10 codes
   - Or manually insert into `icd_codes` table

### Rollback (if needed)

If you need to rollback this migration:

```sql
-- Remove foreign key constraints
ALTER TABLE consultations 
  DROP FOREIGN KEY consultations_ibfk_preliminary_diagnosis,
  DROP FOREIGN KEY consultations_ibfk_final_diagnosis;

-- Remove columns
ALTER TABLE consultations 
  DROP COLUMN preliminary_diagnosis_id,
  DROP COLUMN final_diagnosis_id;

-- Drop table
DROP TABLE IF EXISTS icd_codes;
```

### Support

For issues or questions:
- Check application logs in `logs/` directory
- Review the NMCP guidelines document
- Contact the development team

### Adding More ICD Codes

To add more diagnosis codes, use this template:

```sql
INSERT INTO icd_codes (code, name, description, category) VALUES
('A00.0', 'Cholera due to Vibrio cholerae 01, biovar cholerae', 'Classical cholera', 'Infectious Diseases'),
('A00.1', 'Cholera due to Vibrio cholerae 01, biovar eltor', 'El Tor cholera', 'Infectious Diseases');
```

Common categories:
- Infectious Diseases
- Parasitic Diseases
- Respiratory Diseases
- Cardiovascular Diseases
- Digestive Diseases
- Endocrine Diseases
- Genitourinary Diseases
- Blood Diseases
- Musculoskeletal Diseases
- Skin Diseases
- Eye Diseases
- Ear Diseases
- Pregnancy Conditions
- Symptoms and Signs
