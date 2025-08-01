# Azure Deployment Script for STMS - Malaysia West
# Following: https://learn.microsoft.com/en-us/azure/mysql/flexible-server/tutorial-php-database-app

# Load configuration
source ./azure-config-malaysia.env

echo "🚀 Starting Azure Deployment for STMS Application"
echo "📍 Location: Malaysia West (malaysiawest)"
echo "🏗️  Resource Group: $RESOURCE_GROUP_NAME"

# Step 1: Create Resource Group in Malaysia West
echo "Creating resource group in Malaysia West..."
az group create --name $RESOURCE_GROUP_NAME --location $AZURE_LOCATION

# Step 2: Create MySQL Flexible Server in Malaysia West
echo "Creating MySQL Flexible Server in Malaysia West..."
az mysql flexible-server create \
    --resource-group $RESOURCE_GROUP_NAME \
    --name $DATABASE_SERVER_NAME \
    --location $AZURE_LOCATION \
    --admin-user stmsadmin \
    --admin-password "StmsPassword123!" \
    --sku-name $DB_SKU \
    --version $DB_VERSION \
    --storage-size $DB_STORAGE_SIZE \
    --backup-retention $DB_BACKUP_RETENTION \
    --public-access 0.0.0.0 \
    --yes

# Step 3: Create database
echo "Creating database..."
az mysql flexible-server db create \
    --resource-group $RESOURCE_GROUP_NAME \
    --server-name $DATABASE_SERVER_NAME \
    --database-name $DATABASE_NAME

# Step 4: Create Container Registry in Malaysia West
echo "Creating Azure Container Registry in Malaysia West..."
az acr create \
    --resource-group $RESOURCE_GROUP_NAME \
    --name $CONTAINER_REGISTRY_NAME \
    --location $AZURE_LOCATION \
    --sku Basic \
    --admin-enabled true

# Step 5: Build and push container image
echo "Building and pushing container image..."
az acr build \
    --registry $CONTAINER_REGISTRY_NAME \
    --resource-group $RESOURCE_GROUP_NAME \
    --image stms:latest \
    --file Dockerfile.azure-tutorial \
    .

# Step 6: Create App Service Plan in Malaysia West
echo "Creating App Service Plan in Malaysia West..."
az appservice plan create \
    --resource-group $RESOURCE_GROUP_NAME \
    --name $APP_SERVICE_PLAN \
    --location $AZURE_LOCATION \
    --sku $APP_SERVICE_SKU \
    --is-linux

# Step 7: Create Web App in Malaysia West
echo "Creating Web App in Malaysia West..."
az webapp create \
    --resource-group $RESOURCE_GROUP_NAME \
    --plan $APP_SERVICE_PLAN \
    --name $WEB_APP_NAME \
    --deployment-container-image-name "$CONTAINER_REGISTRY_NAME.azurecr.io/stms:latest"

# Step 8: Configure Web App settings
echo "Configuring Web App settings..."
az webapp config appsettings set \
    --resource-group $RESOURCE_GROUP_NAME \
    --name $WEB_APP_NAME \
    --settings \
        APP_ENV="production" \
        APP_DEBUG="false" \
        APP_KEY="base64:$(openssl rand -base64 32)" \
        DB_CONNECTION="mysql" \
        DB_HOST="$DATABASE_SERVER_NAME.mysql.database.azure.com" \
        DB_PORT="3306" \
        DB_DATABASE="$DATABASE_NAME" \
        DB_USERNAME="stmsadmin" \
        DB_PASSWORD="StmsPassword123!" \
        LOG_CHANNEL="stderr" \
        LOG_LEVEL="info" \
        CACHE_DRIVER="file" \
        SESSION_DRIVER="file" \
        QUEUE_CONNECTION="sync"

# Step 9: Configure container settings
echo "Configuring container settings..."
az webapp config container set \
    --resource-group $RESOURCE_GROUP_NAME \
    --name $WEB_APP_NAME \
    --container-image-name "$CONTAINER_REGISTRY_NAME.azurecr.io/stms:latest" \
    --container-registry-url "https://$CONTAINER_REGISTRY_NAME.azurecr.io"

# Step 10: Configure firewall for Web App access to MySQL
echo "Configuring MySQL firewall..."
WEBAPP_IP=$(az webapp show --resource-group $RESOURCE_GROUP_NAME --name $WEB_APP_NAME --query outboundIpAddresses --output tsv | tr ',' '\n')
for ip in $WEBAPP_IP; do
    az mysql flexible-server firewall-rule create \
        --resource-group $RESOURCE_GROUP_NAME \
        --name $DATABASE_SERVER_NAME \
        --rule-name "WebApp-$ip" \
        --start-ip-address $ip \
        --end-ip-address $ip
done

echo "✅ Deployment completed!"
echo "🌐 Your STMS application is available at: https://$WEB_APP_NAME.azurewebsites.net"
echo "📍 All resources are located in Malaysia West"
echo ""
echo "📝 Next steps:"
echo "1. Test the application"
echo "2. Run database migrations"
echo "3. Set up continuous deployment"
