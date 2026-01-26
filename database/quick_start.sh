#!/bin/bash

# Quick Start Script for IPD & Radiology Testing
# This script helps you quickly set up test users and verify the installation

set -e

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Directory of this script (absolute path)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}  IPD & Radiology Modules - Quick Start${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo ""

# Check if MySQL is accessible
echo -e "${YELLOW}Checking database connection...${NC}"
if mysql -u root -p -e "USE zahanati;" 2>/dev/null; then
    echo -e "${GREEN}✓ Database connection successful${NC}"
else
    echo -e "${RED}✗ Cannot connect to database${NC}"
    echo "Please ensure MySQL is running and zahanati database exists"
    exit 1
fi

# Verify tables exist
echo -e "\n${YELLOW}Verifying IPD & Radiology tables...${NC}"
TABLE_COUNT=$(mysql -u root -p -N -e "
    SELECT COUNT(*) FROM information_schema.tables 
    WHERE table_schema='zahanati' 
    AND (table_name LIKE 'ipd_%' OR table_name LIKE 'radiology_%');
" 2>/dev/null)

if [ "$TABLE_COUNT" -eq 9 ]; then
    echo -e "${GREEN}✓ All 9 tables found (4 radiology + 5 IPD)${NC}"
else
    echo -e "${RED}✗ Expected 9 tables, found $TABLE_COUNT${NC}"
    echo "Run the setup script first: ./database/setup_ipd_radiology.sh"
    exit 1
fi

# Show current data
echo -e "\n${YELLOW}Current data summary:${NC}"
mysql -u root -p -e "
    SELECT 'Radiology Tests' as Item, COUNT(*) as Count FROM zahanati.radiology_tests
    UNION ALL
    SELECT 'IPD Wards', COUNT(*) FROM zahanati.ipd_wards
    UNION ALL
    SELECT 'IPD Beds', COUNT(*) FROM zahanati.ipd_beds
    UNION ALL
    SELECT 'Available Beds', COUNT(*) FROM zahanati.ipd_beds WHERE status='available';
" 2>/dev/null

# Ask to create test users
echo -e "\n${YELLOW}Do you want to create test users?${NC}"
echo "This will create:"
echo "  - Radiologist user (radiologist1 / password)"
echo "  - Nurse user (nurse1 / password)"
echo "  - Add nurse role to existing receptionist"
echo -n "Create test users? (y/n): "
read -r CREATE_USERS

if [[ $CREATE_USERS =~ ^[Yy]$ ]]; then
    echo -e "\n${YELLOW}Creating test users...${NC}"
    SQL_FILE="$SCRIPT_DIR/create_test_users.sql"
    if [ ! -f "$SQL_FILE" ]; then
        echo -e "${RED}✗ Test SQL file not found: $SQL_FILE${NC}"
        echo "Expected to find the file in the database folder."
    elif mysql -u root -p zahanati < "$SQL_FILE" 2>/dev/null; then
        echo -e "${GREEN}✓ Test users created successfully${NC}"
        echo ""
        echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
        echo -e "${GREEN}Test Login Credentials:${NC}"
        echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
        echo -e "Radiologist: ${GREEN}radiologist1${NC} / ${GREEN}password${NC}"
        echo -e "Nurse:       ${GREEN}nurse1${NC} / ${GREEN}password${NC}"
        echo -e "Receptionist: Check existing user (now has nurse role too)"
        echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
    else
        echo -e "${RED}✗ Failed to create test users${NC}"
    fi
fi

# Show next steps
echo -e "\n${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}Next Steps:${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
echo ""
echo "1. Access the application: http://localhost/KJ/"
echo ""
echo "2. Login as Radiologist:"
echo "   - Username: radiologist1"
echo "   - Password: password"
echo "   - Navigate to: Radiology → Dashboard"
echo ""
echo "3. Login as Nurse:"
echo "   - Username: nurse1"
echo "   - Password: password"
echo "   - Navigate to: IPD → Dashboard"
echo ""
echo "4. Test Workflows:"
echo "   - Radiology: View orders → Perform test → Record result"
echo "   - IPD: View beds → Admit patient → Add progress note → Discharge"
echo ""
echo "5. Documentation:"
echo "   - Testing Guide: docs/TESTING_IPD_RADIOLOGY.md"
echo "   - Implementation: docs/IMPLEMENTATION_COMPLETE.md"
echo ""
echo -e "${GREEN}Setup complete! Happy testing!${NC}"
echo ""
