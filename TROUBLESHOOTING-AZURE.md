# STMS Azure Container Apps - Troubleshooting Guide

This guide helps you diagnose and fix common issues with STMS deployment on Azure Container Apps.

## Quick Diagnostics

### Check Application Status
```bash
# Get container app status
az containerapp show -n stms-app -g stms-rg --query properties.runningStatus

# Get recent logs
az containerapp logs show -n stms-app -g stms-rg --tail 50

# Follow live logs
az containerapp logs show -n stms-app -g stms-rg --follow
```

### Test Endpoints
```bash
# Test your deployed app (replace with your actual URL)
curl https://stms-app.eastus.azurecontainerapps.io/health
curl https://stms-app.eastus.azurecontainerapps.io/startup-probe
curl https://stms-app.eastus.azurecontainerapps.io/readiness
```

## Common Issues and Solutions

### 1. Container App Not Starting

**Symptoms:**
- Container app shows "Failed" or "Unhealthy" status
- Application is not accessible

**Possible Causes & Solutions:**

#### A. Port Configuration Issue
```bash
# Check current configuration
az containerapp show -n stms-app -g stms-rg --query properties.configuration.ingress.targetPort

# If not 8080, update it
az containerapp update -n stms-app -g stms-rg --target-port 8080
```

#### B. Image Build Issues
```bash
# Rebuild the image with verbose output
az acr build --registry stmsacr --image stms:latest --file Dockerfile.azure . --verbose

# Check if image exists
az acr repository show-tags --name stmsacr --repository stms
```

#### C. Environment Variables Missing
```bash
# Check current environment variables
az containerapp show -n stms-app -g stms-rg --query properties.template.containers[0].env

# Add missing environment variables
az containerapp update -n stms-app -g stms-rg --set-env-vars \
    DB_HOST="stms-mysql-server.mysql.database.azure.com" \
    DB_USERNAME="stmsadmin" \
    DB_PASSWORD="StmsSecure@2024!" \
    APP_KEY="base64:$(openssl rand -base64 32)"
```

### 2. Database Connection Issues

**Symptoms:**
- Logs show "Connection refused" or "Access denied" errors
- Migrations fail to run

**Solutions:**

#### A. Check Database Server Status
```bash
# Check if MySQL server is running
az mysql flexible-server show -n stms-mysql-server -g stms-rg --query state

# Restart if needed
az mysql flexible-server restart -n stms-mysql-server -g stms-rg
```

#### B. Verify Firewall Rules
```bash
# List current firewall rules
az mysql flexible-server firewall-rule list -n stms-mysql-server -g stms-rg

# Add rule for Azure services if missing
az mysql flexible-server firewall-rule create \
    --resource-group stms-rg \
    --name stms-mysql-server \
    --rule-name AllowAzureServices \
    --start-ip-address 0.0.0.0 \
    --end-ip-address 0.0.0.0
```

#### C. Test Database Connection
```bash
# Connect to container app for testing
az containerapp exec -n stms-app -g stms-rg --command /bin/bash

# Inside the container, test MySQL connection
mysql -h stms-mysql-server.mysql.database.azure.com -u stmsadmin -p -e "SELECT 1;"
```

### 3. Migration and Seeding Failures

**Symptoms:**
- Application starts but shows database errors
- Admin user login doesn't work
- Missing tables or data

**Solutions:**

#### A. Manual Migration
```bash
# Execute commands in the running container
az containerapp exec -n stms-app -g stms-rg --command /bin/bash

# Inside the container:
cd /var/www/html
php artisan migrate --force
php artisan db:seed --force --class=AdminUserSeeder
```

#### B. Reset Database (Destructive - Use with Caution)
```bash
# Drop and recreate database
az mysql flexible-server db delete -n stms-mysql-server -g stms-rg --database-name stms --yes
az mysql flexible-server db create -n stms-mysql-server -g stms-rg --database-name stms

# Restart container app to trigger migrations
az containerapp revision restart -n stms-app -g stms-rg
```

#### C. Check Migration Files
```bash
# List migration files in the container
az containerapp exec -n stms-app -g stms-rg --command "ls -la /var/www/html/database/migrations/"

# Check Laravel migration status
az containerapp exec -n stms-app -g stms-rg --command "php /var/www/html/artisan migrate:status"
```

### 4. Performance Issues

**Symptoms:**
- Slow response times
- Timeouts
- High resource usage

**Solutions:**

#### A. Scale the Application
```bash
# Increase replica count
az containerapp update -n stms-app -g stms-rg --min-replicas 2 --max-replicas 5

# Increase resource allocation
az containerapp update -n stms-app -g stms-rg --cpu 2.0 --memory 4Gi
```

#### B. Database Performance
```bash
# Upgrade database tier
az mysql flexible-server update -n stms-mysql-server -g stms-rg --sku-name Standard_B2s

# Enable slow query log
az mysql flexible-server parameter set -n stms-mysql-server -g stms-rg --name slow_query_log --value ON
```

#### C. Application Optimization
```bash
# Clear and rebuild Laravel caches
az containerapp exec -n stms-app -g stms-rg --command "php /var/www/html/artisan optimize:clear"
az containerapp exec -n stms-app -g stms-rg --command "php /var/www/html/artisan optimize"
```

### 5. SSL/HTTPS Issues

**Symptoms:**
- Mixed content warnings
- SSL certificate errors
- Redirect loops

**Solutions:**

#### A. Force HTTPS in Laravel
Add to your `.env` or environment variables:
```bash
az containerapp update -n stms-app -g stms-rg --set-env-vars \
    FORCE_HTTPS="true" \
    APP_URL="https://stms-app.eastus.azurecontainerapps.io"
```

#### B. Custom Domain (Optional)
```bash
# Add custom domain
az containerapp hostname add -n stms-app -g stms-rg --hostname yourdomain.com

# Configure SSL certificate
az containerapp ssl upload -n stms-app -g stms-rg --hostname yourdomain.com --certificate-file cert.pfx
```

### 6. Storage and File Upload Issues

**Symptoms:**
- File uploads fail
- Storage permission errors
- Disk space issues

**Solutions:**

#### A. Check Storage Permissions
```bash
# Check storage directory permissions
az containerapp exec -n stms-app -g stms-rg --command "ls -la /var/www/html/storage/"

# Fix permissions if needed
az containerapp exec -n stms-app -g stms-rg --command "chmod -R 755 /var/www/html/storage/"
```

#### B. Configure Azure Blob Storage (Recommended for Production)
```bash
# Create storage account
az storage account create -n stmsstorage -g stms-rg --sku Standard_LRS

# Get connection string
STORAGE_CONNECTION=$(az storage account show-connection-string -n stmsstorage -g stms-rg --query connectionString -o tsv)

# Update app with storage configuration
az containerapp update -n stms-app -g stms-rg --set-env-vars \
    FILESYSTEM_DISK="azure" \
    AZURE_STORAGE_CONNECTION_STRING="$STORAGE_CONNECTION"
```

## Monitoring and Alerts

### Set Up Log Analytics Queries
```kusto
// Container App logs
ContainerAppConsoleLogs_CL
| where ContainerAppName_s == "stms-app"
| order by TimeGenerated desc
| limit 100

// Error logs only
ContainerAppConsoleLogs_CL
| where ContainerAppName_s == "stms-app"
| where Log_s contains "ERROR" or Log_s contains "CRITICAL"
| order by TimeGenerated desc
```

### Create Alerts
```bash
# Create alert for container app failures
az monitor metrics alert create \
    --name "STMS App Down" \
    --resource-group stms-rg \
    --scopes "/subscriptions/{subscription-id}/resourceGroups/stms-rg/providers/Microsoft.App/containerApps/stms-app" \
    --condition "avg Replicas < 1" \
    --description "STMS Container App has no running replicas"
```

## Recovery Procedures

### Complete Application Reset
```bash
# 1. Stop the application
az containerapp update -n stms-app -g stms-rg --min-replicas 0 --max-replicas 0

# 2. Reset database (if needed)
az mysql flexible-server db delete -n stms-mysql-server -g stms-rg --database-name stms --yes
az mysql flexible-server db create -n stms-mysql-server -g stms-rg --database-name stms

# 3. Rebuild and push image
az acr build --registry stmsacr --image stms:recovery --file Dockerfile.azure .

# 4. Update container app with new image
az containerapp update -n stms-app -g stms-rg --image stmsacr.azurecr.io/stms:recovery

# 5. Restart application
az containerapp update -n stms-app -g stms-rg --min-replicas 1 --max-replicas 3
```

### Backup and Restore Database
```bash
# Backup database
mysqldump -h stms-mysql-server.mysql.database.azure.com -u stmsadmin -p stms > backup.sql

# Restore database (if needed)
mysql -h stms-mysql-server.mysql.database.azure.com -u stmsadmin -p stms < backup.sql
```

## Getting Help

### Collect Diagnostic Information
```bash
# Container app information
az containerapp show -n stms-app -g stms-rg > container-app-info.json

# Recent logs
az containerapp logs show -n stms-app -g stms-rg --tail 200 > app-logs.txt

# Database server information
az mysql flexible-server show -n stms-mysql-server -g stms-rg > database-info.json

# Resource group resources
az resource list -g stms-rg --output table > resources.txt
```

### Useful Commands for Support
```bash
# Get container app revisions
az containerapp revision list -n stms-app -g stms-rg --output table

# Get container app environment info
az containerapp env show -n stms-env -g stms-rg

# Check resource usage
az monitor metrics list --resource "/subscriptions/{subscription-id}/resourceGroups/stms-rg/providers/Microsoft.App/containerApps/stms-app" --metric "CpuPercentage,MemoryPercentage"
```

Remember to replace placeholder values like resource group names, subscription IDs, and URLs with your actual values.
