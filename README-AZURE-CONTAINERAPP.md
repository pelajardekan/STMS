# STMS - Azure Container Apps Deployment Guide

This guide provides instructions for deploying STMS to Azure Container Apps with proper database migration handling.

## Prerequisites

1. **Azure CLI** installed and logged in
2. **Docker** installed locally (optional, for local testing)
3. **Git** for version control
4. An active Azure subscription

## Quick Deployment (Recommended)

### Option 1: Automated Deployment with PowerShell (Windows)

1. **Clone the repository and navigate to the project directory:**
   ```powershell
   cd STMS
   ```

2. **Run the automated deployment script:**
   ```powershell
   .\azure-deploy-containerapp.ps1
   ```

### Option 2: Automated Deployment with Bash (Linux/macOS/WSL)

1. **Clone the repository and navigate to the project directory:**
   ```bash
   cd STMS
   ```

2. **Make the deployment script executable:**
   ```bash
   chmod +x azure-deploy-containerapp.sh
   ```

3. **Run the automated deployment:**
   ```bash
   ./azure-deploy-containerapp.sh
   ```

## What the Deployment Script Does

The automated deployment script will:

1. **Create Azure Resources:**
   - Resource Group
   - Log Analytics Workspace
   - Azure Container Registry (ACR)
   - MySQL Flexible Server
   - Container Apps Environment
   - Container App

2. **Build and Deploy:**
   - Build the Docker image using `Dockerfile.azure`
   - Push the image to Azure Container Registry
   - Deploy the container app with proper configuration

3. **Database Setup:**
   - Create MySQL Flexible Server with proper firewall rules
   - Configure database connection
   - Automatically run migrations and seeders on container startup

## Key Improvements for Azure Container Apps

### 1. Azure-Optimized Dockerfile (`Dockerfile.azure`)
- Uses port 8080 (required by Azure Container Apps)
- Optimized startup sequence with database connection waiting
- Proper health checks and readiness probes
- Automatic migration and seeding on startup
- Logging to stdout/stderr for Azure monitoring

### 2. Container Apps Specific Configuration
- **Port Configuration:** Uses port 8080 instead of 80
- **Health Checks:** Implements `/health`, `/startup-probe`, and `/readiness` endpoints
- **Environment Variables:** Properly configured for Azure environment
- **Logging:** All logs go to stdout/stderr for Azure Log Analytics

### 3. Database Migration Strategy
- **Automatic Migration:** Runs migrations on container startup
- **Connection Testing:** Waits for database to be ready before running migrations
- **Graceful Seeding:** Uses `updateOrCreate` to handle duplicate admin users
- **Error Handling:** Continues operation even if seeding fails (for existing data)

## Manual Deployment Steps

If you prefer to deploy manually or understand the process:

### 1. Create Resource Group
```bash
az group create --name stms-rg --location eastus
```

### 2. Install Container Apps Extension
```bash
az extension add --name containerapp --upgrade
```

### 3. Register Providers
```bash
az provider register --namespace Microsoft.App
az provider register --namespace Microsoft.OperationalInsights
```

### 4. Create Log Analytics Workspace
```bash
az monitor log-analytics workspace create \
    --resource-group stms-rg \
    --workspace-name stms-logs \
    --location eastus
```

### 5. Create Azure Container Registry
```bash
az acr create \
    --resource-group stms-rg \
    --name stmsacr \
    --sku Basic \
    --admin-enabled true
```

### 6. Build and Push Image
```bash
az acr build \
    --registry stmsacr \
    --image stms:latest \
    --file Dockerfile.azure \
    .
```

### 7. Create MySQL Database
```bash
az mysql flexible-server create \
    --resource-group stms-rg \
    --name stms-mysql-server \
    --location eastus \
    --admin-user stmsadmin \
    --admin-password "StmsSecure@2024!" \
    --sku-name Standard_B1ms \
    --tier Burstable \
    --storage-size 32 \
    --version 8.0.21 \
    --public-access 0.0.0.0 \
    --yes

az mysql flexible-server db create \
    --resource-group stms-rg \
    --server-name stms-mysql-server \
    --database-name stms
```

### 8. Create Container Apps Environment
```bash
az containerapp env create \
    --name stms-env \
    --resource-group stms-rg \
    --location eastus \
    --logs-workspace-id <workspace-id> \
    --logs-workspace-key <workspace-key>
```

### 9. Deploy Container App
```bash
az containerapp create \
    --name stms-app \
    --resource-group stms-rg \
    --environment stms-env \
    --image stmsacr.azurecr.io/stms:latest \
    --target-port 8080 \
    --ingress external \
    --min-replicas 1 \
    --max-replicas 3 \
    --cpu 1.0 \
    --memory 2Gi
```

## Post-Deployment

### Default Admin User
After deployment, you can log in with:
- **Email:** adminpjp@gmail.com
- **Password:** 12345678

### Monitoring and Troubleshooting

1. **View Logs:**
   ```bash
   az containerapp logs show -n stms-app -g stms-rg --follow
   ```

2. **Scale Application:**
   ```bash
   az containerapp update -n stms-app -g stms-rg --min-replicas 2 --max-replicas 5
   ```

3. **Restart Application:**
   ```bash
   az containerapp revision restart -n stms-app -g stms-rg
   ```

4. **Update Application:**
   ```bash
   # Build new image
   az acr build --registry stmsacr --image stms:v2 --file Dockerfile.azure .
   
   # Update container app
   az containerapp update -n stms-app -g stms-rg --image stmsacr.azurecr.io/stms:v2
   ```

## Troubleshooting Common Issues

### 1. Migration Failures
If migrations fail:
- Check database connection settings
- Verify firewall rules allow Azure services
- Check logs for specific error messages

### 2. Container Startup Issues
- Ensure port 8080 is used (not 80)
- Check health check endpoints are accessible
- Verify environment variables are set correctly

### 3. Database Connection Issues
- Verify MySQL server is running
- Check firewall rules
- Ensure connection string is correct

## Environment Variables

The following environment variables are automatically configured:

```env
APP_NAME=STMS
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.region.azurecontainerapps.io
LOG_CHANNEL=stderr
LOG_LEVEL=info
DB_CONNECTION=mysql
DB_HOST=stms-mysql-server.mysql.database.azure.com
DB_PORT=3306
DB_DATABASE=stms
DB_USERNAME=stmsadmin
DB_PASSWORD=StmsSecure@2024!
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

## Cost Optimization

1. **Scale to Zero:** Configure minimum replicas to 0 for development environments
2. **Right-size Resources:** Adjust CPU and memory based on actual usage
3. **Database Tier:** Use appropriate MySQL tier for your workload

## Security Considerations

1. **Database Password:** Change the default database password
2. **Admin User:** Change the default admin password after first login
3. **Environment Variables:** Use Azure Key Vault for sensitive configuration
4. **Network Security:** Configure virtual networks for production environments

## Next Steps

1. Set up custom domain and SSL certificate
2. Configure automated backups for MySQL database
3. Set up monitoring and alerts
4. Implement CI/CD pipeline for automated deployments
5. Configure scaling rules based on metrics
