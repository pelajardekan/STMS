# Azure Setup Script for STMS
# Run this script after installing Azure CLI

Write-Host "🚀 Setting up Azure resources for STMS..." -ForegroundColor Green

# Configuration
$RESOURCE_GROUP = "stms-rg"
$LOCATION = "eastus"
$ACR_NAME = "stmsacr"
$APP_SERVICE_PLAN = "stms-plan"
$APP_NAME = "stms-app"
$DB_SERVER = "stms-mysql-server"
$DB_NAME = "stms"

# Login to Azure
Write-Host "📝 Logging in to Azure..." -ForegroundColor Yellow
az login

# Create Resource Group
Write-Host "📦 Creating resource group..." -ForegroundColor Yellow
az group create --name $RESOURCE_GROUP --location $LOCATION

# Create Azure Container Registry
Write-Host "🏗️ Creating Azure Container Registry..." -ForegroundColor Yellow
az acr create --resource-group $RESOURCE_GROUP --name $ACR_NAME --sku Basic --admin-enabled true

# Get ACR credentials
Write-Host "🔑 Getting ACR credentials..." -ForegroundColor Yellow
$ACR_LOGIN_SERVER = az acr show --name $ACR_NAME --resource-group $RESOURCE_GROUP --query "loginServer" --output tsv
$ACR_USERNAME = az acr credential show --name $ACR_NAME --query "username" --output tsv
$ACR_PASSWORD = az acr credential show --name $ACR_NAME --query "passwords[0].value" --output tsv

# Create MySQL Flexible Server
Write-Host "🗄️ Creating MySQL Flexible Server..." -ForegroundColor Yellow
az mysql flexible-server create `
    --resource-group $RESOURCE_GROUP `
    --name $DB_SERVER `
    --location $LOCATION `
    --admin-user stmsadmin `
    --admin-password "Stms@2024!" `
    --sku-name Standard_B1ms `
    --tier Burstable `
    --storage-size 20 `
    --version 8.0.21

# Create database
Write-Host "📊 Creating database..." -ForegroundColor Yellow
az mysql flexible-server db create `
    --resource-group $RESOURCE_GROUP `
    --server-name $DB_SERVER `
    --database-name $DB_NAME

# Configure MySQL firewall
Write-Host "🔥 Configuring MySQL firewall..." -ForegroundColor Yellow
az mysql flexible-server firewall-rule create `
    --resource-group $RESOURCE_GROUP `
    --name $DB_SERVER `
    --rule-name AllowAzureServices `
    --start-ip-address 0.0.0.0 `
    --end-ip-address 0.0.0.0

# Create App Service Plan
Write-Host "📋 Creating App Service Plan..." -ForegroundColor Yellow
az appservice plan create `
    --resource-group $RESOURCE_GROUP `
    --name $APP_SERVICE_PLAN `
    --sku B1 `
    --is-linux

# Create Web App
Write-Host "🌐 Creating Web App..." -ForegroundColor Yellow
az webapp create `
    --resource-group $RESOURCE_GROUP `
    --plan $APP_SERVICE_PLAN `
    --name $APP_NAME `
    --deployment-container-image-name $ACR_LOGIN_SERVER/stms:latest

# Configure Web App
Write-Host "⚙️ Configuring Web App..." -ForegroundColor Yellow
az webapp config set `
    --resource-group $RESOURCE_GROUP `
    --name $APP_NAME `
    --linux-fx-version "DOCKER|$ACR_LOGIN_SERVER/stms:latest"

# Set environment variables
Write-Host "🔧 Setting environment variables..." -ForegroundColor Yellow
az webapp config appsettings set `
    --resource-group $RESOURCE_GROUP `
    --name $APP_NAME `
    --settings `
    APP_ENV=production `
    APP_DEBUG=false `
    DB_CONNECTION=mysql `
    DB_HOST="$DB_SERVER.mysql.database.azure.com" `
    DB_PORT=3306 `
    DB_DATABASE=$DB_NAME `
    DB_USERNAME=stmsadmin `
    DB_PASSWORD="Stms@2024!" `
    CACHE_DRIVER=file `
    SESSION_DRIVER=file `
    QUEUE_CONNECTION=sync

# Create Service Principal for GitHub Actions
Write-Host "🔐 Creating Service Principal for GitHub Actions..." -ForegroundColor Yellow
$SP_OUTPUT = az ad sp create-for-rbac --name "stms-github-actions" --role contributor --scopes /subscriptions/$(az account show --query id -o tsv)/resourceGroups/$RESOURCE_GROUP --sdk-auth

Write-Host "✅ Azure setup complete!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Next steps:" -ForegroundColor Cyan
Write-Host "1. Copy the Service Principal JSON above" -ForegroundColor White
Write-Host "2. Go to your GitHub repository settings" -ForegroundColor White
Write-Host "3. Add these secrets:" -ForegroundColor White
Write-Host "   - AZURE_CREDENTIALS: (the JSON from step 1)" -ForegroundColor White
Write-Host "   - REGISTRY_USERNAME: $ACR_USERNAME" -ForegroundColor White
Write-Host "   - REGISTRY_PASSWORD: $ACR_PASSWORD" -ForegroundColor White
Write-Host ""
Write-Host "🌐 Your app will be available at: https://$APP_NAME.azurewebsites.net" -ForegroundColor Green 