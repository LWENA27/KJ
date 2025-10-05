#!/bin/bash

# Development script for KJ Healthcare System
# This script runs Tailwind CSS in watch mode for development

echo "🚀 Starting KJ Healthcare System development mode..."
echo "👀 Watching for changes in your PHP files..."
echo "🔄 Tailwind CSS will rebuild automatically when you make changes"
echo ""
echo "Press Ctrl+C to stop watching"
echo ""

cd /var/www/html/KJ
npx tailwindcss -i ./assets/css/input.css -o ./assets/css/tailwind.css --watch
