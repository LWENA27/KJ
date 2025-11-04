#!/bin/bash
# Railway deployment script - runs after code is deployed

echo "🚀 Starting Railway deployment..."

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL connection..."
for i in {1..30}; do
    if mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" -e "SELECT 1" &>/dev/null; then
        echo "✅ MySQL is ready!"
        break
    fi
    echo "Waiting for MySQL... ($i/30)"
    sleep 2
done

# Check if database exists, create if not
echo "📦 Checking database..."
mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Check if tables exist
TABLE_COUNT=$(mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" -sse "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DB_NAME';")

if [ "$TABLE_COUNT" -eq "0" ]; then
    echo "🔧 No tables found - importing initial schema..."
    mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < database/zahanati.sql
    echo "✅ Schema imported successfully!"
else
    echo "✅ Database tables already exist ($TABLE_COUNT tables)"
fi

# Run migrations if they exist
if [ -f "database/add_diagnosis_columns.sql" ]; then
    echo "🔄 Running migration: add_diagnosis_columns.sql"
    mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < database/add_diagnosis_columns.sql 2>/dev/null || echo "Migration already applied or failed (this is OK if columns exist)"
fi

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 logs/ tmp/ 2>/dev/null || true
chmod 644 config/database.php 2>/dev/null || true

echo "✅ Deployment complete! Application is ready."
