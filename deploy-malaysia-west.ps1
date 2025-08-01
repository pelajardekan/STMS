# Azure Deployment Script for STMS - Malaysia West (PowerShell)
# Following: https://learn.microsoft.com/en-us/azure/mysql/flexible-server/tutorial-php-database-app

# Configuration for Malaysia West
$AZURE_LOCATION = "malaysiawest"
$RESOURCE_GROUP_NAME = "stms-rg-myw"
$APP_SERVICE_PLAN = "stms-plan-myw"
$WEB_APP_NAME = "stms-app-myw"
$DATABASE_SERVER_NAME = "stms-mysql-myw"
$DATABASE_NAME = "stms"
$CONTAINER_REGISTRY_NAME = "stmsacrmyw"
$DB_SKU = "Standard_B1ms"
$APP_SERVICE_SKU = "B1"

Write-Host "🚀 Starting Azure Deployment for STMS Application" -ForegroundColor Green
Write-Host "📍 Location: Malaysia West (malaysiawest)" -ForegroundColor Yellow
Write-Host "🏗️  Resource Group: $RESOURCE_GROUP_NAME" -ForegroundColor Yellow

# Step 1: Create Resource Group in Malaysia West
Write-Host "Creating resource group in Malaysia West..." -ForegroundColor Cyan
az group create --name $RESOURCE_GROUP_NAME --location $AZURE_LOCATION

# Step 2: Create MySQL Flexible Server in Malaysia West
Write-Host "Creating MySQL Flexible Server in Malaysia West..." -ForegroundColor Cyan
az mysql flexible-server create `
    --resource-group $RESOURCE_GROUP_NAME `
    --name $DATABASE_SERVER_NAME `
    --location $AZURE_LOCATION `
    --admin-user stmsadmin `
    --admin-password "StmsPassword123!" `
    --sku-name $DB_SKU `
    --version "8.0" `
    --storage-size 20 `
    --backup-retention 7 `
    --public-access 0.0.0.0 `
    --yes

# Step 3: Create database
Write-Host "Creating database..." -ForegroundColor Cyan
az mysql flexible-server db create `
    --resource-group $RESOURCE_GROUP_NAME `
    --server-name $DATABASE_SERVER_NAME `
    --database-name $DATABASE_NAME

# Step 4: Create Container Registry in Malaysia West
Write-Host "Creating Azure Container Registry in Malaysia West..." -ForegroundColor Cyan
az acr create `
    --resource-group $RESOURCE_GROUP_NAME `
    --name $CONTAINER_REGISTRY_NAME `
    --location $AZURE_LOCATION `
    --sku Basic `
    --admin-enabled true

# Step 5: Build and push container image
Write-Host "Building and pushing container image..." -ForegroundColor Cyan
az acr build `
    --registry $CONTAINER_REGISTRY_NAME `
    --resource-group $RESOURCE_GROUP_NAME `
    --image stms:latest `
    --file Dockerfile.azure-tutorial `
    .

# Step 6: Create App Service Plan in Malaysia West
Write-Host "Creating App Service Plan in Malaysia West..." -ForegroundColor Cyan
az appservice plan create `
    --resource-group $RESOURCE_GROUP_NAME `
    --name $APP_SERVICE_PLAN `
    --location $AZURE_LOCATION `
    --sku $APP_SERVICE_SKU `
    --is-linux

# Step 7: Create Web App in Malaysia West
Write-Host "Creating Web App in Malaysia West..." -ForegroundColor Cyan
az webapp create `
    --resource-group $RESOURCE_GROUP_NAME `
    --plan $APP_SERVICE_PLAN `
    --name $WEB_APP_NAME `
    --deployment-container-image-name "$CONTAINER_REGISTRY_NAME.azurecr.io/stms:latest"

# Step 8: Generate Laravel App Key
$APP_KEY = "base64:$(([Convert]::ToBase64String([System.Text.Encoding]::UTF8.GetBytes((1..32 | ForEach-Object { [char]((65..90) + (97..122) | Get-Random) }) -join ''))))"

# Step 9: Configure Web App settings
Write-Host "Configuring Web App settings..." -ForegroundColor Cyan
az webapp config appsettings set `
    --resource-group $RESOURCE_GROUP_NAME `
    --name $WEB_APP_NAME `
    --settings `
        APP_ENV="production" `
        APP_DEBUG="false" `
        APP_KEY="$APP_KEY" `
        DB_CONNECTION="mysql" `
        DB_HOST="$DATABASE_SERVER_NAME.mysql.database.azure.com" `
        DB_PORT="3306" `
        DB_DATABASE="$DATABASE_NAME" `
        DB_USERNAME="stmsadmin" `
        DB_PASSWORD="StmsPassword123!" `
        LOG_CHANNEL="stderr" `
        LOG_LEVEL="info" `
        CACHE_DRIVER="file" `
        SESSION_DRIVER="file" `
        QUEUE_CONNECTION="sync"

# Step 10: Configure container settings
Write-Host "Configuring container settings..." -ForegroundColor Cyan
az webapp config container set `
    --resource-group $RESOURCE_GROUP_NAME `
    --name $WEB_APP_NAME `
    --container-image-name "$CONTAINER_REGISTRY_NAME.azurecr.io/stms:latest" `
    --container-registry-url "https://$CONTAINER_REGISTRY_NAME.azurecr.io"

# Step 11: Configure MySQL firewall for Web App
Write-Host "Configuring MySQL firewall..." -ForegroundColor Cyan
$WEBAPP_IPS = (az webapp show --resource-group $RESOURCE_GROUP_NAME --name $WEB_APP_NAME --query outboundIpAddresses --output tsv) -split ','
foreach ($ip in $WEBAPP_IPS) {
    az mysql flexible-server firewall-rule create `
        --resource-group $RESOURCE_GROUP_NAME `
        --name $DATABASE_SERVER_NAME `
        --rule-name "WebApp-$ip" `
        --start-ip-address $ip `
        --end-ip-address $ip
}

Write-Host "✅ Deployment completed!" -ForegroundColor Green
Write-Host "🌐 Your STMS application is available at: https://$WEB_APP_NAME.azurewebsites.net" -ForegroundColor Green
Write-Host "📍 All resources are located in Malaysia West" -ForegroundColor Yellow
Write-Host ""
Write-Host "📝 Next steps:" -ForegroundColor Cyan
Write-Host "1. Test the application" -ForegroundColor White
Write-Host "2. Run database migrations" -ForegroundColor White
Write-Host "3. Set up continuous deployment" -ForegroundColor White
