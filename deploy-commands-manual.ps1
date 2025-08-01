# Quick Deployment Commands for STMS - Malaysia West
# Run these commands one by one if you prefer manual control

# Current Status: ✅ Resource Group created, ✅ MySQL Server created, ✅ Database created
# Remaining steps:

# 1. Create Container Registry
az acr create --resource-group stms-rg-myw --name stmsacrmyw --sku Basic --admin-enabled true --location malaysiawest

# 2. Build and push Docker image
az acr build --registry stmsacrmyw --image stms:latest --file Dockerfile.azure-tutorial .

# 3. Create App Service Plan
az appservice plan create --name stms-plan-myw --resource-group stms-rg-myw --location malaysiawest --sku B1 --is-linux

# 4. Get ACR login server
$acrServer = az acr show --name stmsacrmyw --resource-group stms-rg-myw --query loginServer --output tsv

# 5. Create Web App
az webapp create --resource-group stms-rg-myw --plan stms-plan-myw --name stms-app-myw --deployment-container-image-name "$acrServer/stms:latest"

# 6. Get ACR credentials
$acrUser = az acr credential show --name stmsacrmyw --query username --output tsv
$acrPass = az acr credential show --name stmsacrmyw --query passwords[0].value --output tsv

# 7. Configure container settings
az webapp config container set --name stms-app-myw --resource-group stms-rg-myw --docker-custom-image-name "$acrServer/stms:latest" --docker-registry-server-url "https://$acrServer" --docker-registry-server-user $acrUser --docker-registry-server-password $acrPass

# 8. Configure app settings
az webapp config appsettings set --resource-group stms-rg-myw --name stms-app-myw --settings APP_ENV="production" APP_DEBUG="false" LOG_CHANNEL="stderr" LOG_LEVEL="info" DB_CONNECTION="mysql" DB_HOST="stms-mysql-myw.mysql.database.azure.com" DB_PORT="3306" DB_DATABASE="stms" DB_USERNAME="stmsadmin" DB_PASSWORD="StmsSecure123!" CACHE_DRIVER="file" SESSION_DRIVER="file" QUEUE_CONNECTION="sync" SESSION_DOMAIN=".azurewebsites.net" SANCTUM_STATEFUL_DOMAINS="*.azurewebsites.net" WEBSITES_PORT="8080"

# 9. Restart the app
az webapp restart --name stms-app-myw --resource-group stms-rg-myw

# 10. Get the URL
az webapp show --name stms-app-myw --resource-group stms-rg-myw --query defaultHostName --output tsv
