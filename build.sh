#!/bin/bash

# Build script for KJ Healthcare System
# This script rebuilds the Tailwind CSS for offline use

echo "🏗️  Building KJ Healthcare System assets..."

# Navigate to project directory
cd /var/www/html/KJ

# Build Tailwind CSS
echo "📦 Building Tailwind CSS..."
npx tailwindcss -i ./assets/css/input.css -o ./assets/css/tailwind.css --minify

# Check if build was successful
if [ $? -eq 0 ]; then
    echo "✅ Tailwind CSS built successfully!"
    echo "📊 File size: $(du -h assets/css/tailwind.css | cut -f1)"
else
    echo "❌ Failed to build Tailwind CSS"
    exit 1
fi

echo "🎉 Build complete! Your app is now ready for offline use."
echo ""
echo "📁 Local assets:"
echo "   - Tailwind CSS: assets/css/tailwind.css"
echo "   - Font Awesome: assets/css/fontawesome.min.css"
echo "   - Fonts: assets/css/fonts.css"
echo ""
echo "💡 To rebuild after making changes:"
echo "   bash build.sh"
