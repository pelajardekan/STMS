# STMS Deployment Guide - Malaysia West

## ✅ Pre-Deployment Checklist

Your STMS application is now **100% ready** for deployment to **Malaysia West** region following the official Microsoft Azure tutorial.

### 🌏 Location Configuration
- **Primary Region**: Malaysia West (`malaysiawest`)
- **Fallback Region**: Malaysia (`malaysia`) - if needed
- **Time Zone**: UTC+8 (Malaysia Time)

### 📋 Deployment Options

#### Option 1: Automated PowerShell Deployment (Recommended)
```powershell
# Run the automated deployment script
.\deploy-malaysia-west.ps1
```

#### Option 2: Azure Portal Web App + Database Wizard
1. Open [Azure Portal](https://portal.azure.com)
2. Search for "Web App + Database"
3. Click "Create"
4. Configure:
   - **Location**: Malaysia West
   - **Runtime**: Container
   - **Container Image**: Upload `Dockerfile.azure-tutorial`
   - **Database**: MySQL Flexible Server
   - **Database Location**: Malaysia West

#### Option 3: Manual Azure CLI Commands
```bash
# Use the shell script for Linux/Mac
./deploy-malaysia-west.sh
```

### 🔧 Key Configuration Settings

**Application Settings** (automatically configured):
```
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=stms-mysql-myw.mysql.database.azure.com
DB_PORT=3306
DB_DATABASE=stms
LOG_CHANNEL=stderr
LOG_LEVEL=info
```

**Container Settings**:
- Port: 8080
- Image: Dockerfile.azure-tutorial
- Health Check: /health endpoint
- Startup Time: ~60 seconds

**Database Settings**:
- Server: MySQL 8.0 Flexible Server
- Location: Malaysia West
- SKU: Standard_B1ms (Burstable)
- Storage: 20GB
- Backup Retention: 7 days

### 🚀 Deployment Steps

1. **Clean Environment**: ✅ Done
2. **Validate Configuration**: Run validation script
3. **Deploy Infrastructure**: Use preferred deployment method
4. **Test Application**: Verify health endpoint
5. **Run Migrations**: Database setup
6. **Configure Monitoring**: Enable Application Insights

### 📊 Expected Deployment Time
- Infrastructure Creation: ~10-15 minutes
- Container Build & Deploy: ~5-10 minutes
- Database Setup: ~5 minutes
- **Total**: ~20-30 minutes

### 🔍 Post-Deployment Verification

After deployment, verify:
1. Application URL: `https://stms-app-myw.azurewebsites.net`
2. Health Check: `https://stms-app-myw.azurewebsites.net/health`
3. Database Connection: Check logs for successful migration
4. SSL Certificate: Automatic Azure-managed certificate

### 🛠️ Troubleshooting

**Common Issues**:
- Container startup timeout: Check health endpoint configuration
- Database connection issues: Verify firewall rules
- SSL certificate: Wait 5-10 minutes for automatic provisioning

**Debug Commands**:
```bash
# Check deployment status
az webapp show --name stms-app-myw --resource-group stms-rg-myw

# View application logs
az webapp log tail --name stms-app-myw --resource-group stms-rg-myw

# Check database connectivity
az mysql flexible-server show --name stms-mysql-myw --resource-group stms-rg-myw
```

### 📞 Support
- Azure Documentation: [PHP Database App Tutorial](https://learn.microsoft.com/en-us/azure/mysql/flexible-server/tutorial-php-database-app)
- Laravel on Azure: [Best Practices Guide](https://docs.microsoft.com/en-us/azure/app-service/quickstart-php)

---
**🌟 Your STMS application is ready for production deployment in Malaysia West!**
