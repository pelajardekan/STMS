# Complete Azure Deployment Script for STMS - Malaysia West
# This script follows the Microsoft Azure PHP Database App Tutorial
# https://learn.microsoft.com/en-us/azure/mysql/flexible-server/tutorial-php-database-app

Write-Host "🚀 Starting complete STMS deployment to Malaysia West..." -ForegroundColor Green

# Load configuration
$ResourceGroup = "stms-rg-myw"
$Location = "malaysiawest"
$MySQLServer = "stms-mysql-myw"
$DatabaseName = "stms"
$ContainerRegistry = "stmsacrmyw"
$AppServicePlan = "stms-plan-myw"
$WebAppName = "stms-app-myw"

Write-Host "📋 Configuration loaded for Malaysia West deployment" -ForegroundColor Yellow

# Step 1: Create Azure Container Registry
Write-Host "🏗️  Step 1: Creating Azure Container Registry..." -ForegroundColor Cyan
az acr create --resource-group $ResourceGroup --name $ContainerRegistry --sku Basic --admin-enabled true --location $Location

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Failed to create Container Registry" -ForegroundColor Red
    exit 1
}

# Step 2: Get ACR login server
Write-Host "🔑 Getting ACR login server..." -ForegroundColor Cyan
$AcrLoginServer = az acr show --name $ContainerRegistry --resource-group $ResourceGroup --query loginServer --output tsv

Write-Host "📝 ACR Login Server: $AcrLoginServer" -ForegroundColor Green

# Step 3: Build and push Docker image
Write-Host "🐳 Step 2: Building and pushing Docker image..." -ForegroundColor Cyan
az acr build --registry $ContainerRegistry --image stms:latest --file Dockerfile.azure-tutorial .

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Failed to build and push Docker image" -ForegroundColor Red
    exit 1
}

# Step 4: Create App Service Plan
Write-Host "📦 Step 3: Creating App Service Plan..." -ForegroundColor Cyan
az appservice plan create --name $AppServicePlan --resource-group $ResourceGroup --location $Location --sku B1 --is-linux

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Failed to create App Service Plan" -ForegroundColor Red
    exit 1
}

# Step 5: Create Web App
Write-Host "🌐 Step 4: Creating Web App..." -ForegroundColor Cyan
az webapp create --resource-group $ResourceGroup --plan $AppServicePlan --name $WebAppName --deployment-container-image-name "$AcrLoginServer/stms:latest"

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Failed to create Web App" -ForegroundColor Red
    exit 1
}

# Step 6: Configure ACR credentials for Web App
Write-Host "🔐 Step 5: Configuring ACR credentials..." -ForegroundColor Cyan
$AcrUsername = az acr credential show --name $ContainerRegistry --query username --output tsv
$AcrPassword = az acr credential show --name $ContainerRegistry --query passwords[0].value --output tsv

az webapp config container set --name $WebAppName --resource-group $ResourceGroup --docker-custom-image-name "$AcrLoginServer/stms:latest" --docker-registry-server-url "https://$AcrLoginServer" --docker-registry-server-user $AcrUsername --docker-registry-server-password $AcrPassword

# Step 7: Configure Web App Settings
Write-Host "⚙️  Step 6: Configuring Web App environment variables..." -ForegroundColor Cyan
az webapp config appsettings set --resource-group $ResourceGroup --name $WebAppName --settings `
    APP_ENV="production" `
    APP_DEBUG="false" `
    LOG_CHANNEL="stderr" `
    LOG_LEVEL="info" `
    DB_CONNECTION="mysql" `
    DB_HOST="$MySQLServer.mysql.database.azure.com" `
    DB_PORT="3306" `
    DB_DATABASE="$DatabaseName" `
    DB_USERNAME="stmsadmin" `
    DB_PASSWORD="StmsSecure123!" `
    CACHE_DRIVER="file" `
    SESSION_DRIVER="file" `
    QUEUE_CONNECTION="sync" `
    SESSION_DOMAIN=".azurewebsites.net" `
    SANCTUM_STATEFUL_DOMAINS="*.azurewebsites.net" `
    WEBSITES_PORT="8080"

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Failed to configure Web App settings" -ForegroundColor Red
    exit 1
}

# Step 8: Restart Web App
Write-Host "🔄 Step 7: Restarting Web App..." -ForegroundColor Cyan
az webapp restart --name $WebAppName --resource-group $ResourceGroup

# Step 9: Get Web App URL
Write-Host "🌍 Step 8: Getting Web App URL..." -ForegroundColor Cyan
$WebAppUrl = az webapp show --name $WebAppName --resource-group $ResourceGroup --query defaultHostName --output tsv

Write-Host "✅ Deployment completed successfully!" -ForegroundColor Green
Write-Host "🔗 Your STMS application is available at: https://$WebAppUrl" -ForegroundColor Yellow
Write-Host "📊 Database: $MySQLServer.mysql.database.azure.com" -ForegroundColor Yellow
Write-Host "🐳 Container Registry: $AcrLoginServer" -ForegroundColor Yellow

# Step 10: Run database migrations (optional)
Write-Host "🗄️  Would you like to run database migrations? (This requires Laravel Artisan)" -ForegroundColor Cyan
$RunMigrations = Read-Host "Run migrations? (y/n)"

if ($RunMigrations -eq "y" -or $RunMigrations -eq "Y") {
    Write-Host "🚀 Running database migrations..." -ForegroundColor Cyan
    az webapp ssh --name $WebAppName --resource-group $ResourceGroup --command "cd /var/www/html && php artisan migrate --force"
}

Write-Host "🎉 Malaysia West deployment complete!" -ForegroundColor Green
Write-Host "📝 Next steps:" -ForegroundColor Yellow
Write-Host "   1. Test your application at https://$WebAppUrl" -ForegroundColor White
Write-Host "   2. Check application logs if needed: az webapp log tail --name $WebAppName --resource-group $ResourceGroup" -ForegroundColor White
Write-Host "   3. Monitor performance in Azure Portal" -ForegroundColor White
