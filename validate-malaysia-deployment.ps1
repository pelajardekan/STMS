# Validation Script for Malaysia West Deployment (PowerShell)

Write-Host "🔍 Validating STMS Deployment Configuration for Malaysia West" -ForegroundColor Green
Write-Host "============================================================" -ForegroundColor Green

# Check Azure CLI login
Write-Host "Checking Azure CLI authentication..." -ForegroundColor Cyan
try {
    $currentSubscription = az account show --query name -o tsv
    Write-Host "✅ Authenticated to Azure subscription: $currentSubscription" -ForegroundColor Green
} catch {
    Write-Host "❌ Not authenticated to Azure. Please run: az login" -ForegroundColor Red
    exit 1
}

# Check if Malaysia West is available
Write-Host "Checking Malaysia West region availability..." -ForegroundColor Cyan
$availableLocations = az account list-locations --query "[?name=='malaysiaawest'].displayName" -o tsv
if ($availableLocations) {
    Write-Host "✅ Malaysia West region is available" -ForegroundColor Green
} else {
    Write-Host "❌ Malaysia West region not available in your subscription" -ForegroundColor Red
    Write-Host "Available regions in Malaysia:" -ForegroundColor Yellow
    az account list-locations --query "[?contains(displayName,``'Malaysia``')].{DisplayName:displayName,Name:name}" -o table
    exit 1
}

# Check Docker installation
Write-Host "Checking Docker installation..." -ForegroundColor Cyan
if (Get-Command docker -ErrorAction SilentlyContinue) {
    $dockerVersion = docker --version
    Write-Host "✅ Docker installed: $dockerVersion" -ForegroundColor Green
} else {
    Write-Host "❌ Docker not installed. Please install Docker Desktop" -ForegroundColor Red
    exit 1
}

# Validate Dockerfile
Write-Host "Validating Dockerfile.azure-tutorial..." -ForegroundColor Cyan
if (Test-Path "Dockerfile.azure-tutorial") {
    $dockerfileContent = Get-Content "Dockerfile.azure-tutorial" -Raw
    if ($dockerfileContent -match "EXPOSE 8080") {
        Write-Host "✅ Dockerfile configured for Azure App Service (port 8080)" -ForegroundColor Green
    } else {
        Write-Host "❌ Dockerfile missing port 8080 configuration" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "❌ Dockerfile.azure-tutorial not found" -ForegroundColor Red
    exit 1
}

# Validate configuration files
Write-Host "Validating configuration files..." -ForegroundColor Cyan
$configFiles = @(
    "docker/nginx-production.conf",
    "docker/php-fpm-production.conf", 
    "docker/php-production.ini",
    "docker/supervisord-azure.conf"
)

foreach ($file in $configFiles) {
    if (Test-Path $file) {
        Write-Host "✅ Found: $file" -ForegroundColor Green
    } else {
        Write-Host "❌ Missing: $file" -ForegroundColor Red
        exit 1
    }
}

# Check nginx configuration for port 8080
$nginxContent = Get-Content "docker/nginx-production.conf" -Raw
if ($nginxContent -match "listen 8080") {
    Write-Host "✅ Nginx configured for port 8080" -ForegroundColor Green
} else {
    Write-Host "❌ Nginx not configured for port 8080" -ForegroundColor Red
    exit 1
}

# Validate Laravel requirements
Write-Host "Validating Laravel application..." -ForegroundColor Cyan
if ((Test-Path "composer.json") -and (Test-Path "artisan")) {
    Write-Host "✅ Laravel application structure valid" -ForegroundColor Green
} else {
    Write-Host "❌ Laravel application files missing" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "🎉 All validations passed!" -ForegroundColor Green
Write-Host "✅ Ready to deploy STMS to Malaysia West" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Run deployment script: ./deploy-malaysia-west.ps1" -ForegroundColor White
Write-Host "2. Or follow the Azure Portal wizard with these settings:" -ForegroundColor White
Write-Host "   - Location: Malaysia West" -ForegroundColor Yellow
Write-Host "   - Container Image: Use Dockerfile.azure-tutorial" -ForegroundColor Yellow
Write-Host "   - Port: 8080" -ForegroundColor Yellow
Write-Host "   - MySQL: Enable with Malaysia West location" -ForegroundColor Yellow
