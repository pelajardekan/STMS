# Azure Container Apps deployment script for STMS (PowerShell version)
# This script deploys STMS to Azure Container Apps with proper migration handling

param(
    [Parameter(Mandatory=$false)]
    [string]$ResourceGroup = "stms-rg",
    
    [Parameter(Mandatory=$false)]
    [string]$Location = "eastus",
    
    [Parameter(Mandatory=$false)]
    [string]$DbPassword = "StmsSecure@2024!"
)

$ErrorActionPreference = "Stop"

# Configuration
$ACR_NAME = "stmsacr" + (Get-Date).ToString("mmss")
$CONTAINER_APP_ENV = "stms-env"
$CONTAINER_APP_NAME = "stms-app"
$DB_SERVER = "stms-mysql-server"
$DB_NAME = "stms"
$DB_USERNAME = "stmsadmin"

Write-Host "🚀 Starting Azure Container Apps deployment for STMS..." -ForegroundColor Green
Write-Host "Resource Group: $ResourceGroup" -ForegroundColor Yellow
Write-Host "Location: $Location" -ForegroundColor Yellow
Write-Host "ACR Name: $ACR_NAME" -ForegroundColor Yellow

# Check if Azure CLI is installed
try {
    $null = az --version
} catch {
    Write-Host "❌ Azure CLI is not installed. Please install it first." -ForegroundColor Red
    exit 1
}

# Check if logged in
try {
    $null = az account show
} catch {
    Write-Host "❌ Not logged into Azure. Please run 'az login' first." -ForegroundColor Red
    exit 1
}

# Create Resource Group
Write-Host "📦 Creating resource group..." -ForegroundColor Yellow
az group create --name $ResourceGroup --location $Location

# Install Container Apps extension
Write-Host "🔧 Installing Container Apps extension..." -ForegroundColor Yellow
az extension add --name containerapp --upgrade

# Register required providers
Write-Host "📋 Registering required providers..." -ForegroundColor Yellow
az provider register --namespace Microsoft.App
az provider register --namespace Microsoft.OperationalInsights

# Create Log Analytics workspace
Write-Host "📊 Creating Log Analytics workspace..." -ForegroundColor Yellow
$LOG_ANALYTICS_WORKSPACE = "stms-logs"
az monitor log-analytics workspace create `
    --resource-group $ResourceGroup `
    --workspace-name $LOG_ANALYTICS_WORKSPACE `
    --location $Location

# Get Log Analytics workspace details
$LOG_ANALYTICS_WORKSPACE_ID = az monitor log-analytics workspace show `
    --resource-group $ResourceGroup `
    --workspace-name $LOG_ANALYTICS_WORKSPACE `
    --query customerId `
    --output tsv

$LOG_ANALYTICS_WORKSPACE_KEY = az monitor log-analytics workspace get-shared-keys `
    --resource-group $ResourceGroup `
    --workspace-name $LOG_ANALYTICS_WORKSPACE `
    --query primarySharedKey `
    --output tsv

# Create Azure Container Registry
Write-Host "🏗️ Creating Azure Container Registry..." -ForegroundColor Yellow
az acr create `
    --resource-group $ResourceGroup `
    --name $ACR_NAME `
    --sku Basic `
    --admin-enabled true

# Get ACR login server and credentials
$ACR_LOGIN_SERVER = az acr show --name $ACR_NAME --resource-group $ResourceGroup --query "loginServer" --output tsv
$ACR_USERNAME = az acr credential show --name $ACR_NAME --query "username" --output tsv
$ACR_PASSWORD = az acr credential show --name $ACR_NAME --query "passwords[0].value" --output tsv

Write-Host "ACR Login Server: $ACR_LOGIN_SERVER" -ForegroundColor Green

# Build and push Docker image using Azure-optimized Dockerfile
Write-Host "🐳 Building and pushing Docker image to ACR..." -ForegroundColor Yellow
az acr build `
    --registry $ACR_NAME `
    --image stms:latest `
    --file Dockerfile.azure `
    .

# Create MySQL Flexible Server
Write-Host "🗄️ Creating MySQL Flexible Server..." -ForegroundColor Yellow
az mysql flexible-server create `
    --resource-group $ResourceGroup `
    --name $DB_SERVER `
    --location $Location `
    --admin-user $DB_USERNAME `
    --admin-password $DbPassword `
    --sku-name Standard_B1ms `
    --tier Burstable `
    --storage-size 32 `
    --version 8.0.21 `
    --public-access 0.0.0.0 `
    --yes

# Create database
Write-Host "📊 Creating database..." -ForegroundColor Yellow
az mysql flexible-server db create `
    --resource-group $ResourceGroup `
    --server-name $DB_SERVER `
    --database-name $DB_NAME

# Configure MySQL firewall for Azure services
Write-Host "🔥 Configuring MySQL firewall..." -ForegroundColor Yellow
az mysql flexible-server firewall-rule create `
    --resource-group $ResourceGroup `
    --name $DB_SERVER `
    --rule-name AllowAzureServices `
    --start-ip-address 0.0.0.0 `
    --end-ip-address 0.0.0.0

# Create Container Apps environment
Write-Host "🌐 Creating Container Apps environment..." -ForegroundColor Yellow
az containerapp env create `
    --name $CONTAINER_APP_ENV `
    --resource-group $ResourceGroup `
    --location $Location `
    --logs-workspace-id $LOG_ANALYTICS_WORKSPACE_ID `
    --logs-workspace-key $LOG_ANALYTICS_WORKSPACE_KEY

# Create Container App with proper configuration
Write-Host "📱 Creating Container App..." -ForegroundColor Yellow
az containerapp create `
    --name $CONTAINER_APP_NAME `
    --resource-group $ResourceGroup `
    --environment $CONTAINER_APP_ENV `
    --image "$ACR_LOGIN_SERVER/stms:latest" `
    --registry-server $ACR_LOGIN_SERVER `
    --registry-username $ACR_USERNAME `
    --registry-password $ACR_PASSWORD `
    --target-port 8080 `
    --ingress external `
    --min-replicas 1 `
    --max-replicas 3 `
    --cpu 1.0 `
    --memory 2Gi `
    --env-vars `
        APP_NAME="STMS" `
        APP_ENV="production" `
        APP_DEBUG="false" `
        APP_URL="https://$CONTAINER_APP_NAME.$Location.azurecontainerapps.io" `
        LOG_CHANNEL="stderr" `
        LOG_LEVEL="info" `
        DB_CONNECTION="mysql" `
        DB_HOST="$DB_SERVER.mysql.database.azure.com" `
        DB_PORT="3306" `
        DB_DATABASE="$DB_NAME" `
        DB_USERNAME="$DB_USERNAME" `
        DB_PASSWORD="$DbPassword" `
        CACHE_DRIVER="file" `
        SESSION_DRIVER="file" `
        QUEUE_CONNECTION="sync" `
        BROADCAST_DRIVER="log" `
        FILESYSTEM_DISK="local" `
        MAIL_MAILER="log" `
        TZ="UTC"

# Wait for container app to be ready
Write-Host "⏳ Waiting for Container App to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 30

# Get the Container App URL
$CONTAINER_APP_URL = az containerapp show `
    --name $CONTAINER_APP_NAME `
    --resource-group $ResourceGroup `
    --query properties.configuration.ingress.fqdn `
    --output tsv

Write-Host "✅ Deployment completed successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "🌐 Your STMS application is available at: https://$CONTAINER_APP_URL" -ForegroundColor Green
Write-Host "🗄️ Database server: $DB_SERVER.mysql.database.azure.com" -ForegroundColor Cyan
Write-Host "📋 Database name: $DB_NAME" -ForegroundColor Cyan
Write-Host "👤 Database username: $DB_USERNAME" -ForegroundColor Cyan
Write-Host ""
Write-Host "📝 Important notes:" -ForegroundColor Yellow
Write-Host "1. The application will automatically run migrations and seeders on startup"
Write-Host "2. Admin user will be created with email: adminpjp@gmail.com and password: 12345678"
Write-Host "3. Monitor logs using: az containerapp logs show -n $CONTAINER_APP_NAME -g $ResourceGroup --follow"
Write-Host "4. To update the application, build and push a new image, then update the container app"
Write-Host ""
Write-Host "🔧 Useful commands:" -ForegroundColor Cyan
Write-Host "Monitor logs: az containerapp logs show -n $CONTAINER_APP_NAME -g $ResourceGroup --follow"
Write-Host "Scale app: az containerapp update -n $CONTAINER_APP_NAME -g $ResourceGroup --min-replicas 1 --max-replicas 5"
Write-Host "Restart app: az containerapp revision restart -n $CONTAINER_APP_NAME -g $ResourceGroup"
Write-Host ""
Write-Host "🎉 STMS deployment to Azure Container Apps is complete!" -ForegroundColor Green
