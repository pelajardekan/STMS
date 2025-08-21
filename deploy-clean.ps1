# STMS Clean Deployment Script v6-stable
# Simple deployment without database operations

Write-Host "=== STMS Clean Deployment v6-stable ===" -ForegroundColor Green

# Variables
$ImageTag = "v6-stable-$(Get-Date -Format 'yyyyMMddHHmmss')"
$AcrName = "stmsregistry"
$AppName = "stms-app-202508012336"
$ResourceGroup = "stms"

Write-Host "Building clean v6-stable container..." -ForegroundColor Yellow
Write-Host "Image Tag: $ImageTag" -ForegroundColor Cyan

# Build container
docker build -t stms-app:$ImageTag -f Dockerfile.v6-stable .

if ($LASTEXITCODE -ne 0) {
    Write-Host "Docker build failed!" -ForegroundColor Red
    exit 1
}

Write-Host "Container built successfully!" -ForegroundColor Green

# Login to ACR
Write-Host "Logging into Azure Container Registry..." -ForegroundColor Yellow
az acr login --name $AcrName

if ($LASTEXITCODE -ne 0) {
    Write-Host "ACR login failed!" -ForegroundColor Red
    exit 1
}

# Tag and push images
Write-Host "Tagging and pushing to ACR..." -ForegroundColor Yellow
docker tag stms-app:$ImageTag "$AcrName.azurecr.io/stms-app:$ImageTag"
docker tag stms-app:$ImageTag "$AcrName.azurecr.io/stms-app:latest"

docker push "$AcrName.azurecr.io/stms-app:$ImageTag"
docker push "$AcrName.azurecr.io/stms-app:latest"

if ($LASTEXITCODE -ne 0) {
    Write-Host "Docker push failed!" -ForegroundColor Red
    exit 1
}

Write-Host "Images pushed successfully!" -ForegroundColor Green

# Deploy to App Service
Write-Host "Deploying to Azure App Service..." -ForegroundColor Yellow
az webapp config container set `
    --resource-group $ResourceGroup `
    --name $AppName `
    --docker-custom-image-name "$AcrName.azurecr.io/stms-app:$ImageTag" `
    --docker-registry-server-url "https://$AcrName.azurecr.io"

if ($LASTEXITCODE -ne 0) {
    Write-Host "App Service deployment failed!" -ForegroundColor Red
    exit 1
}

Write-Host "Waiting for deployment to stabilize..." -ForegroundColor Yellow
Start-Sleep -Seconds 30

Write-Host "=== DEPLOYMENT COMPLETED SUCCESSFULLY ===" -ForegroundColor Green
Write-Host "Application URL: https://$AppName.azurewebsites.net" -ForegroundColor Cyan
Write-Host "Container Image: $AcrName.azurecr.io/stms-app:$ImageTag" -ForegroundColor Cyan
Write-Host "" -ForegroundColor White
Write-Host "Test the application:" -ForegroundColor Yellow
Write-Host "  Health Check: https://$AppName.azurewebsites.net/health" -ForegroundColor White
Write-Host "  Application: https://$AppName.azurewebsites.net" -ForegroundColor White
