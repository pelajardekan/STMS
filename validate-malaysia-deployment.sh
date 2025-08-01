#!/bin/bash
# Validation Script for Malaysia West Deployment

echo "🔍 Validating STMS Deployment Configuration for Malaysia West"
echo "============================================================"

# Check Azure CLI login
echo "Checking Azure CLI authentication..."
if az account show >/dev/null 2>&1; then
    CURRENT_SUBSCRIPTION=$(az account show --query name -o tsv)
    echo "✅ Authenticated to Azure subscription: $CURRENT_SUBSCRIPTION"
else
    echo "❌ Not authenticated to Azure. Please run: az login"
    exit 1
fi

# Check if Malaysia West is available
echo "Checking Malaysia West region availability..."
AVAILABLE_LOCATIONS=$(az account list-locations --query "[?name=='malaysiaawest'].displayName" -o tsv)
if [ ! -z "$AVAILABLE_LOCATIONS" ]; then
    echo "✅ Malaysia West region is available"
else
    echo "❌ Malaysia West region not available in your subscription"
    echo "Available regions in Malaysia:"
    az account list-locations --query "[?contains(displayName, 'Malaysia')].{DisplayName:displayName, Name:name}" -o table
    exit 1
fi

# Check Docker installation
echo "Checking Docker installation..."
if command -v docker >/dev/null 2>&1; then
    DOCKER_VERSION=$(docker --version)
    echo "✅ Docker installed: $DOCKER_VERSION"
else
    echo "❌ Docker not installed. Please install Docker Desktop"
    exit 1
fi

# Validate Dockerfile
echo "Validating Dockerfile.azure-tutorial..."
if [ -f "Dockerfile.azure-tutorial" ]; then
    if grep -q "EXPOSE 8080" Dockerfile.azure-tutorial; then
        echo "✅ Dockerfile configured for Azure App Service (port 8080)"
    else
        echo "❌ Dockerfile missing port 8080 configuration"
        exit 1
    fi
else
    echo "❌ Dockerfile.azure-tutorial not found"
    exit 1
fi

# Validate configuration files
echo "Validating configuration files..."
CONFIG_FILES=("docker/nginx-production.conf" "docker/php-fpm-production.conf" "docker/php-production.ini" "docker/supervisord-azure.conf")
for file in "${CONFIG_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ Found: $file"
    else
        echo "❌ Missing: $file"
        exit 1
    fi
done

# Check nginx configuration for port 8080
if grep -q "listen 8080" docker/nginx-production.conf; then
    echo "✅ Nginx configured for port 8080"
else
    echo "❌ Nginx not configured for port 8080"
    exit 1
fi

# Validate Laravel requirements
echo "Validating Laravel application..."
if [ -f "composer.json" ] && [ -f "artisan" ]; then
    echo "✅ Laravel application structure valid"
else
    echo "❌ Laravel application files missing"
    exit 1
fi

echo ""
echo "🎉 All validations passed!"
echo "✅ Ready to deploy STMS to Malaysia West"
echo ""
echo "Next steps:"
echo "1. Run deployment script: ./deploy-malaysia-west.ps1"
echo "2. Or follow the Azure Portal wizard with these settings:"
echo "   - Location: Malaysia West"
echo "   - Container Image: Use Dockerfile.azure-tutorial"
echo "   - Port: 8080"
echo "   - MySQL: Enable with Malaysia West location"
