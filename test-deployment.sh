#!/bin/bash

# Test script for STMS deployment
# This script tests the deployed application functionality

set -e

if [ -z "$1" ]; then
    echo "Usage: $0 <app-url>"
    echo "Example: $0 https://stms-app.eastus.azurecontainerapps.io"
    exit 1
fi

APP_URL="$1"

echo "🧪 Testing STMS deployment at: $APP_URL"

# Test health endpoint
echo "Testing health endpoint..."
if curl -f "$APP_URL/health" >/dev/null 2>&1; then
    echo "✅ Health endpoint is working"
else
    echo "❌ Health endpoint failed"
    exit 1
fi

# Test startup probe
echo "Testing startup probe..."
if curl -f "$APP_URL/startup-probe" >/dev/null 2>&1; then
    echo "✅ Startup probe is working"
else
    echo "❌ Startup probe failed"
    exit 1
fi

# Test main application
echo "Testing main application..."
if curl -f "$APP_URL" >/dev/null 2>&1; then
    echo "✅ Main application is accessible"
else
    echo "❌ Main application is not accessible"
    exit 1
fi

# Test readiness probe
echo "Testing readiness probe..."
if curl -f "$APP_URL/readiness" >/dev/null 2>&1; then
    echo "✅ Application is ready"
else
    echo "⚠️ Application may not be fully ready yet"
fi

echo ""
echo "🎉 All tests passed! Your STMS application is running correctly."
echo "🌐 Application URL: $APP_URL"
echo "👤 Default admin login: adminpjp@gmail.com / 12345678"
