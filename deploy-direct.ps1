# STMS Direct Deployment Script
# This script deploys your Laravel app directly to Azure without GitHub Actions

Write-Host "Starting STMS Direct Deployment..." -ForegroundColor Green

# Set variables
$ResourceGroup = "stms"
$AppName = "stms-app-202508012336"
$ImageTag = "v$(Get-Date -Format 'yyyyMMddHHmmss')"

Write-Host "Building Docker image..." -ForegroundColor Yellow
docker build -f Dockerfile.simple -t stms-app:latest .

if ($LASTEXITCODE -ne 0) {
    Write-Host "Docker build failed!" -ForegroundColor Red
    exit 1
}

Write-Host "Getting ACR information..." -ForegroundColor Yellow
$AcrName = az acr list --resource-group $ResourceGroup --query "[0].name" -o tsv
Write-Host "ACR Name: $AcrName" -ForegroundColor Cyan

Write-Host "Logging into ACR..." -ForegroundColor Yellow
az acr login --name $AcrName

Write-Host "Tagging and pushing images..." -ForegroundColor Yellow
docker tag stms-app:latest "$AcrName.azurecr.io/stms-app:$ImageTag"
docker tag stms-app:latest "$AcrName.azurecr.io/stms-app:latest"

docker push "$AcrName.azurecr.io/stms-app:$ImageTag"
docker push "$AcrName.azurecr.io/stms-app:latest"

if ($LASTEXITCODE -ne 0) {
    Write-Host "Docker push failed!" -ForegroundColor Red
    exit 1
}

Write-Host "Deploying to App Service..." -ForegroundColor Yellow
az webapp config container set `
    --name $AppName `
    --resource-group $ResourceGroup `
    --container-image-name "$AcrName.azurecr.io/stms-app:$ImageTag"

if ($LASTEXITCODE -ne 0) {
    Write-Host "App Service deployment failed!" -ForegroundColor Red
    exit 1
}

Write-Host "Waiting for deployment to complete..." -ForegroundColor Yellow
Start-Sleep -Seconds 60

Write-Host "Running database migrations..." -ForegroundColor Yellow
az webapp ssh --resource-group $ResourceGroup --name $AppName --timeout 300 -- "cd /var/www/html && php artisan migrate --force"

Write-Host "Running database seeding..." -ForegroundColor Yellow
az webapp ssh --resource-group $ResourceGroup --name $AppName --timeout 300 -- "cd /var/www/html && php artisan db:seed --force"

Write-Host "Deployment completed successfully!" -ForegroundColor Green
Write-Host "Application URL: https://$AppName.azurewebsites.net" -ForegroundColor Cyan
Write-Host "Container Image: $AcrName.azurecr.io/stms-app:$ImageTag" -ForegroundColor Cyan

Write-Host "Test endpoints:" -ForegroundColor Yellow
Write-Host "   - Health: https://$AppName.azurewebsites.net/health"
Write-Host "   - Database Status: https://$AppName.azurewebsites.net/test-db"
