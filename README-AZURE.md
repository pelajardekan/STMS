# STMS - Azure Deployment Guide

This guide will help you deploy the STMS (Space and Tenant Management System) to Azure using Docker containers.

## Prerequisites

1. **Azure CLI** installed and logged in
2. **Docker** installed locally
3. **Git** for version control

## Quick Deployment

### Option 1: Automated Deployment (Recommended)

1. **Clone the repository and navigate to the project directory:**
   ```bash
   cd STMS
   ```

2. **Make the deployment script executable:**
   ```bash
   chmod +x azure-deploy.sh
   ```

3. **Run the automated deployment:**
   ```bash
   ./azure-deploy.sh
   ```

The script will automatically:
- Create a resource group
- Set up Azure Container Registry
- Build and push the Docker image
- Create MySQL Flexible Server
- Deploy the web application
- Configure all necessary settings

### Option 2: Manual Deployment

If you prefer to deploy manually, follow these steps:

#### 1. Create Resource Group
```bash
az group create --name stms-rg --location eastus
```

#### 2. Create Azure Container Registry
```bash
az acr create --resource-group stms-rg --name stmsacr --sku Basic --admin-enabled true
```

#### 3. Build and Push Docker Image
```bash
az acr build --registry stmsacr --image stms:latest .
```

#### 4. Create MySQL Database
```bash
az mysql flexible-server create \
    --resource-group stms-rg \
    --name stms-mysql-server \
    --location eastus \
    --admin-user stmsadmin \
    --admin-password "YourSecurePassword123!" \
    --sku-name Standard_B1ms \
    --tier Burstable \
    --storage-size 20 \
    --version 8.0.21

az mysql flexible-server db create \
    --resource-group stms-rg \
    --server-name stms-mysql-server \
    --database-name stms
```

#### 5. Create App Service
```bash
az appservice plan create \
    --resource-group stms-rg \
    --name stms-plan \
    --sku B1 \
    --is-linux

az webapp create \
    --resource-group stms-rg \
    --plan stms-plan \
    --name stms-app \
    --deployment-container-image-name stmsacr.azurecr.io/stms:latest
```

#### 6. Configure Environment Variables
```bash
az webapp config appsettings set \
    --resource-group stms-rg \
    --name stms-app \
    --settings \
    APP_ENV=production \
    APP_DEBUG=false \
    DB_CONNECTION=mysql \
    DB_HOST="stms-mysql-server.mysql.database.azure.com" \
    DB_PORT=3306 \
    DB_DATABASE=stms \
    DB_USERNAME=stmsadmin \
    DB_PASSWORD="YourSecurePassword123!" \
    CACHE_DRIVER=file \
    SESSION_DRIVER=file \
    QUEUE_CONNECTION=sync
```

## Post-Deployment Setup

### 1. Run Database Migrations

Connect to your web app via SSH:
```bash
az webapp ssh --resource-group stms-rg --name stms-app
```

Run migrations:
```bash
php artisan migrate --force
```

### 2. Create Admin User

Seed the database with admin user:
```bash
php artisan db:seed --class=AdminUserSeeder
```

### 3. Set Up Scheduled Tasks

For production, you should set up Azure WebJobs or use Azure Functions for scheduled tasks:

#### Option A: Azure WebJobs
Create a WebJob to run the scheduled commands:

```bash
# Create a directory for WebJobs
mkdir -p .azure/webjobs/continuous/rental-status-update
mkdir -p .azure/webjobs/continuous/monthly-invoices

# Create the WebJob scripts
echo 'php /home/site/wwwroot/artisan rentals:update-status' > .azure/webjobs/continuous/rental-status-update/run.sh
echo 'php /home/site/wwwroot/artisan invoices:generate-monthly' > .azure/webjobs/continuous/monthly-invoices/run.sh

# Make scripts executable
chmod +x .azure/webjobs/continuous/rental-status-update/run.sh
chmod +x .azure/webjobs/continuous/monthly-invoices/run.sh
```

#### Option B: Azure Functions (Recommended)
Create Azure Functions with Timer triggers for better scalability.

### 4. Configure Storage

For file uploads, configure Azure Blob Storage:

1. Create a Storage Account
2. Create a container for file uploads
3. Update the application configuration to use Azure Blob Storage

## Local Development

### Using Docker Compose

1. **Start the services:**
   ```bash
   docker-compose up -d
   ```

2. **Run migrations:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

3. **Seed the database:**
   ```bash
   docker-compose exec app php artisan db:seed
   ```

4. **Access the application:**
   - Web: http://localhost:8080
   - Database: localhost:3306
   - Redis: localhost:6379

### Using Laravel Sail

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

## Environment Variables

### Required Environment Variables

```env
APP_NAME=STMS
APP_ENV=production
APP_KEY=base64:your-app-key
APP_DEBUG=false
APP_URL=https://your-app.azurewebsites.net

DB_CONNECTION=mysql
DB_HOST=your-mysql-server.mysql.database.azure.com
DB_PORT=3306
DB_DATABASE=stms
DB_USERNAME=stmsadmin
DB_PASSWORD=your-secure-password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

LOG_CHANNEL=stack
LOG_LEVEL=error
```

### Optional Environment Variables

```env
# For Azure Blob Storage
AZURE_STORAGE_ACCOUNT=your-storage-account
AZURE_STORAGE_KEY=your-storage-key
AZURE_STORAGE_CONTAINER=uploads

# For Redis (if using)
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
REDIS_PORT=6379
```

## Monitoring and Logging

### Application Insights

Enable Application Insights for monitoring:

```bash
az monitor app-insights component create \
    --app stms-insights \
    --location eastus \
    --resource-group stms-rg \
    --application-type web
```

### Log Analytics

Configure Log Analytics for centralized logging:

```bash
az monitor log-analytics workspace create \
    --resource-group stms-rg \
    --workspace-name stms-logs
```

## Security Considerations

1. **Use Azure Key Vault** for storing sensitive configuration
2. **Enable HTTPS** and configure SSL certificates
3. **Set up Azure AD** for authentication
4. **Configure Network Security Groups** to restrict access
5. **Enable Azure Security Center** for threat protection

## Scaling

### Vertical Scaling
Upgrade your App Service Plan to a higher tier (S1, S2, P1, P2, etc.)

### Horizontal Scaling
Enable auto-scaling in your App Service Plan:

```bash
az monitor autoscale create \
    --resource-group stms-rg \
    --resource stms-plan \
    --resource-type Microsoft.Web/serverFarms \
    --name stms-autoscale \
    --min-count 1 \
    --max-count 10 \
    --count 1
```

## Backup and Recovery

### Database Backup
Azure MySQL Flexible Server provides automatic backups. Configure backup retention:

```bash
az mysql flexible-server update \
    --resource-group stms-rg \
    --name stms-mysql-server \
    --backup-retention-days 30
```

### Application Backup
Use Azure Backup to backup your application data and configuration.

## Troubleshooting

### Common Issues

1. **Database Connection Issues**
   - Check firewall rules
   - Verify connection string
   - Ensure SSL is properly configured

2. **File Upload Issues**
   - Check storage permissions
   - Verify file size limits
   - Ensure proper file paths

3. **Performance Issues**
   - Enable OPcache
   - Configure Redis for caching
   - Optimize database queries

### Useful Commands

```bash
# View application logs
az webapp log tail --resource-group stms-rg --name stms-app

# SSH into the application
az webapp ssh --resource-group stms-rg --name stms-app

# Restart the application
az webapp restart --resource-group stms-rg --name stms-app

# Check application status
az webapp show --resource-group stms-rg --name stms-app
```

## Support

For issues related to:
- **Azure Services**: Check Azure documentation and support
- **Laravel Application**: Check Laravel documentation
- **Docker**: Check Docker documentation

## Cost Optimization

1. **Use Azure Reserved Instances** for predictable workloads
2. **Enable auto-shutdown** for development environments
3. **Monitor resource usage** and optimize accordingly
4. **Use Azure Hybrid Benefit** if eligible 