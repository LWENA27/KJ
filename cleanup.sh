#!/bin/bash
# Project Cleanup Script
# Run this to improve VS Code performance

echo "🧹 Starting project cleanup..."

# Clear logs
echo "📄 Clearing log files..."
sudo truncate -s 0 logs/application.log
sudo truncate -s 0 logs/php_errors.log

# Remove old session files (older than 1 day)
echo "🗑️  Removing old session files..."
find storage/sessions -type f -mtime +1 -delete 2>/dev/null

# Remove temporary files
echo "🗑️  Removing temporary files..."
find . -name "*~" -type f -delete 2>/dev/null
find . -name "*.tmp" -type f -delete 2>/dev/null
find . -name "*.bak" -type f -delete 2>/dev/null

# Show current project size
echo ""
echo "📊 Project size after cleanup:"
du -sh .

echo ""
echo "✅ Cleanup complete!"
echo "💡 Tip: Reload VS Code window (Ctrl+Shift+P -> 'Reload Window') for best performance"
