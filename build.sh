#!/usr/bin/env bash
# Build script for Render
echo "Starting build process..."

# Create uploads directory if it doesn't exist
mkdir -p assets/images

# Set proper permissions
chmod -R 755 assets/
chmod -R 755 config/
chmod -R 755 admin/

echo "Build completed!"