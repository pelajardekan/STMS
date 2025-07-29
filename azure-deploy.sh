#!/bin/bash

# Azure Container Registry and App Service deployment script
# Make sure you have Azure CLI installed and logged in

# Configuration
RESOURCE_GROUP="stms-rg"
LOCATION="eastus"
ACR_NAME="stmsacr"
APP_SERVICE_PLAN="stms-plan"
APP_NAME="stms-app"
DB_SERVER="stms-mysql-server"
DB_NAME="stms"

echo "🚀 Starting Azure deployment for STMS..."

# Create Resource Group
echo "📦 Creating resource group..."
az group create --name $RESOURCE_GROUP --location $LOCATION

# Create Azure Container Registry
echo "🏗️ Creating Azure Container Registry..."
az acr create --resource-group $RESOURCE_GROUP --name $ACR_NAME --sku Basic --admin-enabled true

# Get ACR login server
ACR_LOGIN_SERVER=$(az acr show --name $ACR_NAME --resource-group $RESOURCE_GROUP --query "loginServer" --output tsv)

# Build and push Docker image
echo "🐳 Building and pushing Docker image..."
az acr build --registry $ACR_NAME --image stms:latest .

# Create MySQL Flexible Server
echo "🗄️ Creating MySQL Flexible Server..."
az mysql flexible-server create \
    --resource-group $RESOURCE_GROUP \
    --name $DB_SERVER \
    --location $LOCATION \
    --admin-user stmsadmin \
    --admin-password "Stms@2024!" \
    --sku-name Standard_B1ms \
    --tier Burstable \
    --storage-size 20 \
    --version 8.0.21

# Create database
echo "📊 Creating database..."
az mysql flexible-server db create \
    --resource-group $RESOURCE_GROUP \
    --server-name $DB_SERVER \
    --database-name $DB_NAME

# Configure MySQL firewall
echo "🔥 Configuring MySQL firewall..."
az mysql flexible-server firewall-rule create \
    --resource-group $RESOURCE_GROUP \
    --name $DB_SERVER \
    --rule-name AllowAzureServices \
    --start-ip-address 0.0.0.0 \
    --end-ip-address 0.0.0.0

# Create App Service Plan
echo "📋 Creating App Service Plan..."
az appservice plan create \
    --resource-group $RESOURCE_GROUP \
    --name $APP_SERVICE_PLAN \
    --sku B1 \
    --is-linux

# Create Web App
echo "🌐 Creating Web App..."
az webapp create \
    --resource-group $RESOURCE_GROUP \
    --plan $APP_SERVICE_PLAN \
    --name $APP_NAME \
    --deployment-container-image-name $ACR_LOGIN_SERVER/stms:latest

# Configure Web App
echo "⚙️ Configuring Web App..."
az webapp config set \
    --resource-group $RESOURCE_GROUP \
    --name $APP_NAME \
    --linux-fx-version "DOCKER|$ACR_LOGIN_SERVER/stms:latest"

# Set environment variables
echo "🔧 Setting environment variables..."
az webapp config appsettings set \
    --resource-group $RESOURCE_GROUP \
    --name $APP_NAME \
    --settings \
    APP_ENV=production \
    APP_DEBUG=false \
    DB_CONNECTION=mysql \
    DB_HOST="$DB_SERVER.mysql.database.azure.com" \
    DB_PORT=3306 \
    DB_DATABASE=$DB_NAME \
    DB_USERNAME=stmsadmin \
    DB_PASSWORD="Stms@2024!" \
    CACHE_DRIVER=file \
    SESSION_DRIVER=file \
    QUEUE_CONNECTION=sync

# Configure continuous deployment
echo "🔄 Configuring continuous deployment..."
az webapp deployment container config \
    --resource-group $RESOURCE_GROUP \
    --name $APP_NAME \
    --enable-cd true

# Get the web app URL
WEBAPP_URL=$(az webapp show --resource-group $RESOURCE_GROUP --name $APP_NAME --query "defaultHostName" --output tsv)

echo "✅ Deployment completed successfully!"
echo "🌐 Your application is available at: https://$WEBAPP_URL"
echo "🗄️ Database connection: $DB_SERVER.mysql.database.azure.com"
echo ""
echo "📝 Next steps:"
echo "1. Run database migrations: az webapp ssh --resource-group $RESOURCE_GROUP --name $APP_NAME"
echo "2. In the SSH session, run: php artisan migrate --force"
echo "3. Create admin user: php artisan db:seed --class=AdminUserSeeder"
echo "4. Set up scheduled tasks for rental status updates and invoice generation" 