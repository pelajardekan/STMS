#!/bin/bash

# Azure Container Apps deployment script for STMS
# This script deploys STMS to Azure Container Apps with proper migration handling

set -e

# Configuration
RESOURCE_GROUP="stms-rg"
LOCATION="eastus"
ACR_NAME="stmsacr$(date +%s | tail -c 6)"  # Add random suffix to avoid conflicts
CONTAINER_APP_ENV="stms-env"
CONTAINER_APP_NAME="stms-app"
DB_SERVER="stms-mysql-server"
DB_NAME="stms"
DB_USERNAME="stmsadmin"
DB_PASSWORD="StmsSecure@2024!"

echo "🚀 Starting Azure Container Apps deployment for STMS..."
echo "Resource Group: $RESOURCE_GROUP"
echo "Location: $LOCATION"
echo "ACR Name: $ACR_NAME"

# Check if Azure CLI is installed and logged in
if ! command -v az &> /dev/null; then
    echo "❌ Azure CLI is not installed. Please install it first."
    exit 1
fi

# Check if logged in
if ! az account show &> /dev/null; then
    echo "❌ Not logged into Azure. Please run 'az login' first."
    exit 1
fi

# Create Resource Group
echo "📦 Creating resource group..."
az group create --name $RESOURCE_GROUP --location $LOCATION

# Install Container Apps extension
echo "🔧 Installing Container Apps extension..."
az extension add --name containerapp --upgrade

# Register required providers
echo "📋 Registering required providers..."
az provider register --namespace Microsoft.App
az provider register --namespace Microsoft.OperationalInsights

# Create Log Analytics workspace
echo "📊 Creating Log Analytics workspace..."
LOG_ANALYTICS_WORKSPACE="stms-logs"
az monitor log-analytics workspace create \
    --resource-group $RESOURCE_GROUP \
    --workspace-name $LOG_ANALYTICS_WORKSPACE \
    --location $LOCATION

# Get Log Analytics workspace details
LOG_ANALYTICS_WORKSPACE_ID=$(az monitor log-analytics workspace show \
    --resource-group $RESOURCE_GROUP \
    --workspace-name $LOG_ANALYTICS_WORKSPACE \
    --query customerId \
    --output tsv)

LOG_ANALYTICS_WORKSPACE_KEY=$(az monitor log-analytics workspace get-shared-keys \
    --resource-group $RESOURCE_GROUP \
    --workspace-name $LOG_ANALYTICS_WORKSPACE \
    --query primarySharedKey \
    --output tsv)

# Create Azure Container Registry
echo "🏗️ Creating Azure Container Registry..."
az acr create \
    --resource-group $RESOURCE_GROUP \
    --name $ACR_NAME \
    --sku Basic \
    --admin-enabled true

# Get ACR login server and credentials
ACR_LOGIN_SERVER=$(az acr show --name $ACR_NAME --resource-group $RESOURCE_GROUP --query "loginServer" --output tsv)
ACR_USERNAME=$(az acr credential show --name $ACR_NAME --query "username" --output tsv)
ACR_PASSWORD=$(az acr credential show --name $ACR_NAME --query "passwords[0].value" --output tsv)

echo "ACR Login Server: $ACR_LOGIN_SERVER"

# Build and push Docker image using Azure-optimized Dockerfile
echo "🐳 Building and pushing Docker image to ACR..."
az acr build \
    --registry $ACR_NAME \
    --image stms:latest \
    --file Dockerfile.azure \
    .

# Create MySQL Flexible Server
echo "🗄️ Creating MySQL Flexible Server..."
az mysql flexible-server create \
    --resource-group $RESOURCE_GROUP \
    --name $DB_SERVER \
    --location $LOCATION \
    --admin-user $DB_USERNAME \
    --admin-password $DB_PASSWORD \
    --sku-name Standard_B1ms \
    --tier Burstable \
    --storage-size 32 \
    --version 8.0.21 \
    --public-access 0.0.0.0 \
    --yes

# Create database
echo "📊 Creating database..."
az mysql flexible-server db create \
    --resource-group $RESOURCE_GROUP \
    --server-name $DB_SERVER \
    --database-name $DB_NAME

# Configure MySQL firewall for Azure services
echo "🔥 Configuring MySQL firewall..."
az mysql flexible-server firewall-rule create \
    --resource-group $RESOURCE_GROUP \
    --name $DB_SERVER \
    --rule-name AllowAzureServices \
    --start-ip-address 0.0.0.0 \
    --end-ip-address 0.0.0.0

# Create Container Apps environment
echo "🌐 Creating Container Apps environment..."
az containerapp env create \
    --name $CONTAINER_APP_ENV \
    --resource-group $RESOURCE_GROUP \
    --location $LOCATION \
    --logs-workspace-id $LOG_ANALYTICS_WORKSPACE_ID \
    --logs-workspace-key $LOG_ANALYTICS_WORKSPACE_KEY

# Create Container App with proper configuration
echo "📱 Creating Container App..."
az containerapp create \
    --name $CONTAINER_APP_NAME \
    --resource-group $RESOURCE_GROUP \
    --environment $CONTAINER_APP_ENV \
    --image $ACR_LOGIN_SERVER/stms:latest \
    --registry-server $ACR_LOGIN_SERVER \
    --registry-username $ACR_USERNAME \
    --registry-password $ACR_PASSWORD \
    --target-port 8080 \
    --ingress external \
    --min-replicas 1 \
    --max-replicas 3 \
    --cpu 1.0 \
    --memory 2Gi \
    --env-vars \
        APP_NAME="STMS" \
        APP_ENV="production" \
        APP_DEBUG="false" \
        APP_URL="https://$CONTAINER_APP_NAME.${LOCATION}.azurecontainerapps.io" \
        LOG_CHANNEL="stderr" \
        LOG_LEVEL="info" \
        DB_CONNECTION="mysql" \
        DB_HOST="$DB_SERVER.mysql.database.azure.com" \
        DB_PORT="3306" \
        DB_DATABASE="$DB_NAME" \
        DB_USERNAME="$DB_USERNAME" \
        DB_PASSWORD="$DB_PASSWORD" \
        CACHE_DRIVER="file" \
        SESSION_DRIVER="file" \
        QUEUE_CONNECTION="sync" \
        BROADCAST_DRIVER="log" \
        FILESYSTEM_DISK="local" \
        MAIL_MAILER="log" \
        TZ="UTC"

# Wait for container app to be ready
echo "⏳ Waiting for Container App to be ready..."
sleep 30

# Get the Container App URL
CONTAINER_APP_URL=$(az containerapp show \
    --name $CONTAINER_APP_NAME \
    --resource-group $RESOURCE_GROUP \
    --query properties.configuration.ingress.fqdn \
    --output tsv)

echo "✅ Deployment completed successfully!"
echo ""
echo "🌐 Your STMS application is available at: https://$CONTAINER_APP_URL"
echo "🗄️ Database server: $DB_SERVER.mysql.database.azure.com"
echo "📋 Database name: $DB_NAME"
echo "👤 Database username: $DB_USERNAME"
echo ""
echo "📝 Important notes:"
echo "1. The application will automatically run migrations and seeders on startup"
echo "2. Admin user will be created with email: adminpjp@gmail.com and password: 12345678"
echo "3. Monitor logs using: az containerapp logs show -n $CONTAINER_APP_NAME -g $RESOURCE_GROUP --follow"
echo "4. To update the application, build and push a new image, then update the container app"
echo ""
echo "🔧 Useful commands:"
echo "Monitor logs: az containerapp logs show -n $CONTAINER_APP_NAME -g $RESOURCE_GROUP --follow"
echo "Scale app: az containerapp update -n $CONTAINER_APP_NAME -g $RESOURCE_GROUP --min-replicas 1 --max-replicas 5"
echo "Restart app: az containerapp revision restart -n $CONTAINER_APP_NAME -g $RESOURCE_GROUP"
echo ""
echo "🎉 STMS deployment to Azure Container Apps is complete!"
