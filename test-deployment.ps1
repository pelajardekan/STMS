# Test script for STMS deployment (PowerShell version)
# This script tests the deployed application functionality

param(
    [Parameter(Mandatory=$true)]
    [string]$AppUrl
)

Write-Host "🧪 Testing STMS deployment at: $AppUrl" -ForegroundColor Green

# Test health endpoint
Write-Host "Testing health endpoint..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "$AppUrl/health" -Method Get -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "✅ Health endpoint is working" -ForegroundColor Green
    } else {
        throw "Health endpoint returned status code: $($response.StatusCode)"
    }
} catch {
    Write-Host "❌ Health endpoint failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Test startup probe
Write-Host "Testing startup probe..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "$AppUrl/startup-probe" -Method Get -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "✅ Startup probe is working" -ForegroundColor Green
    } else {
        throw "Startup probe returned status code: $($response.StatusCode)"
    }
} catch {
    Write-Host "❌ Startup probe failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Test main application
Write-Host "Testing main application..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri $AppUrl -Method Get -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "✅ Main application is accessible" -ForegroundColor Green
    } else {
        throw "Main application returned status code: $($response.StatusCode)"
    }
} catch {
    Write-Host "❌ Main application is not accessible: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Test readiness probe
Write-Host "Testing readiness probe..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "$AppUrl/readiness" -Method Get -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "✅ Application is ready" -ForegroundColor Green
    } else {
        Write-Host "⚠️ Application may not be fully ready yet" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠️ Application may not be fully ready yet: $($_.Exception.Message)" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "🎉 All tests passed! Your STMS application is running correctly." -ForegroundColor Green
Write-Host "🌐 Application URL: $AppUrl" -ForegroundColor Cyan
Write-Host "👤 Default admin login: adminpjp@gmail.com / 12345678" -ForegroundColor Cyan
