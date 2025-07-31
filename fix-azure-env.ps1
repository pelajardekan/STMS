# Fix Azure Environment Variables
Write-Host "🔧 Setting environment variables for STMS Azure deployment..." -ForegroundColor Yellow

$RESOURCE_GROUP = "stms-rg"
$APP_NAME = "stms-app"
$DB_SERVER = "stms-mysql-server"
$DB_NAME = "stms"

# Set environment variables one by one
Write-Host "Setting APP_ENV..." -ForegroundColor Green
az webapp config appsettings set --resource-group $RESOURCE_GROUP --name $APP_NAME --settings APP_ENV=production

Write-Host "Setting APP_DEBUG..." -ForegroundColor Green
az webapp config appsettings set --resource-group $RESOURCE_GROUP --name $APP_NAME --settings APP_DEBUG=false

Write-Host "Setting APP_KEY..." -ForegroundColor Green
az webapp config appsettings set --resource-group $RESOURCE_GROUP --name $APP_NAME --settings APP_KEY="base64:aTHXmsyjmyHKtZY8D9aLqfsa3Ym8r1VS5qtKttYIl88="

Write-Host "Setting database configuration..." -ForegroundColor Green
az webapp config appsettings set --resource-group $RESOURCE_GROUP --name $APP_NAME --settings DB_CONNECTION=mysql
az webapp config appsettings set --resource-group $RESOURCE_GROUP --name $APP_NAME --settings DB_HOST="$DB_SERVER.mysql.database.azure.com"
az webapp config appsettings set --resource-group $RESOURCE_GROUP --name $APP_NAME --settings DB_PORT=3306
az webapp config appsettings set --resource-group $RESOURCE_GROUP --name $APP_NAME --settings DB_DATABASE=$DB_NAME
az webapp config appsettings set --resource-group $RESOURCE_GROUP --name $APP_NAME --settings DB_USERNAME=stmsadmin
az webapp config appsettings set --resource-group $RESOURCE_GROUP --name $APP_NAME --settings DB_PASSWORD="Stms@2024!"

Write-Host "Setting cache and session drivers..." -ForegroundColor Green
az webapp config appsettings set --resource-group $RESOURCE_GROUP --name $APP_NAME --settings CACHE_DRIVER=file
az webapp config appsettings set --resource-group $RESOURCE_GROUP --name $APP_NAME --settings SESSION_DRIVER=file
az webapp config appsettings set --resource-group $RESOURCE_GROUP --name $APP_NAME --settings QUEUE_CONNECTION=sync

Write-Host "Setting Docker port..." -ForegroundColor Green
az webapp config appsettings set --resource-group $RESOURCE_GROUP --name $APP_NAME --settings WEBSITES_PORT=80

Write-Host "✅ Environment variables configured!" -ForegroundColor Green
Write-Host "🔄 Restarting application..." -ForegroundColor Yellow
az webapp restart --resource-group $RESOURCE_GROUP --name $APP_NAME

Write-Host "Your application should be available at: https://$APP_NAME.azurewebsites.net" -ForegroundColor Green
