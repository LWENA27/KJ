#!/bin/bash
# IPD and Radiology Module Setup Script
# This script will run all database migrations and seed data

set -e  # Exit on error

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}========================================${NC}"
echo -e "${YELLOW}IPD & Radiology Module Setup${NC}"
echo -e "${YELLOW}========================================${NC}"
echo ""

# Database credentials (update these if different)
DB_NAME="zahanati"
DB_USER="root"
DB_PATH="/var/www/html/KJ/database"

echo -e "${YELLOW}This script will:${NC}"
echo "1. Add 'nurse' and 'radiologist' roles to ENUM columns"
echo "2. Create 4 radiology tables (categories, tests, orders, results)"
echo "3. Create 5 IPD tables (wards, beds, admissions, progress notes, medication admin)"
echo "4. Seed initial data (test categories, sample tests, wards, beds, permissions)"
echo ""
echo -e "${RED}WARNING: This will modify your database schema!${NC}"
echo ""
read -p "Do you want to continue? (y/n): " confirm

if [[ "$confirm" != "y" && "$confirm" != "yes" ]]; then
    echo -e "${RED}Setup cancelled.${NC}"
    exit 0
fi

echo ""
echo -e "${GREEN}Starting database migration...${NC}"
echo ""

# Migration 001: Add roles to ENUM
echo -e "${YELLOW}[1/4] Adding nurse and radiologist roles to ENUMs...${NC}"
mysql -u "$DB_USER" -p "$DB_NAME" < "$DB_PATH/migrations/001_add_nurse_radiologist_roles.sql"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Migration 001 completed successfully${NC}"
else
    echo -e "${RED}✗ Migration 001 failed${NC}"
    exit 1
fi
echo ""

# Migration 002: Create radiology tables
echo -e "${YELLOW}[2/4] Creating radiology tables...${NC}"
mysql -u "$DB_USER" -p "$DB_NAME" < "$DB_PATH/migrations/002_create_radiology_tables.sql"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Migration 002 completed successfully${NC}"
else
    echo -e "${RED}✗ Migration 002 failed${NC}"
    exit 1
fi
echo ""

# Migration 003: Create IPD tables
echo -e "${YELLOW}[3/4] Creating IPD tables...${NC}"
mysql -u "$DB_USER" -p "$DB_NAME" < "$DB_PATH/migrations/003_create_ipd_tables.sql"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Migration 003 completed successfully${NC}"
else
    echo -e "${RED}✗ Migration 003 failed${NC}"
    exit 1
fi
echo ""

# Seed data
echo -e "${YELLOW}[4/4] Loading seed data...${NC}"
mysql -u "$DB_USER" -p "$DB_NAME" < "$DB_PATH/seeds/001_radiology_ipd_seed.sql"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Seed data loaded successfully${NC}"
else
    echo -e "${RED}✗ Seed data failed${NC}"
    exit 1
fi
echo ""

# Verification
echo -e "${YELLOW}Verifying installation...${NC}"
mysql -u "$DB_USER" -p "$DB_NAME" -e "
SELECT 
    'Tables Created' AS check_type,
    (SELECT COUNT(*) FROM information_schema.tables 
     WHERE table_schema = '$DB_NAME' AND table_name LIKE 'radiology%') AS radiology_tables,
    (SELECT COUNT(*) FROM information_schema.tables 
     WHERE table_schema = '$DB_NAME' AND table_name LIKE 'ipd%') AS ipd_tables;
     
SELECT 
    'Seed Data' AS check_type,
    (SELECT COUNT(*) FROM radiology_test_categories) AS categories,
    (SELECT COUNT(*) FROM radiology_tests) AS tests,
    (SELECT COUNT(*) FROM ipd_wards) AS wards,
    (SELECT COUNT(*) FROM ipd_beds) AS beds;
"

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}✓ Setup completed successfully!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Create controllers: RadiologistController.php and IpdController.php"
echo "2. Create views for radiologist and IPD modules"
echo "3. Update index.php routing"
echo "4. Add role assignment UI in admin panel"
echo "5. Create test multi-role user"
echo ""
echo -e "${YELLOW}See docs/IPD_RADIOLOGY_IMPLEMENTATION_ROADMAP.md for detailed instructions.${NC}"
echo ""
