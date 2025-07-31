# STMS Docker Management Script for PowerShell

param(
    [Parameter(Mandatory=$true)]
    [string]$Command,
    
    [Parameter(Mandatory=$false)]
    [string]$Service
)

switch ($Command.ToLower()) {
    "start" {
        Write-Host "Starting STMS application with database..." -ForegroundColor Green
        docker-compose up -d
        Write-Host "Waiting for services to be ready..." -ForegroundColor Yellow
        Start-Sleep -Seconds 10
        docker-compose ps
        Write-Host ""
        Write-Host "Application should be available at: http://localhost:8080" -ForegroundColor Cyan
        Write-Host "Database: localhost:3306 (user: stms_user, password: stms_password)" -ForegroundColor Cyan
    }
    "stop" {
        Write-Host "Stopping STMS application..." -ForegroundColor Red
        docker-compose down
    }
    "restart" {
        Write-Host "Restarting STMS application..." -ForegroundColor Yellow
        docker-compose down
        docker-compose up -d
    }
    "logs" {
        if ($Service) {
            docker-compose logs -f $Service
        } else {
            docker-compose logs -f
        }
    }
    "status" {
        docker-compose ps
    }
    "build" {
        Write-Host "Building STMS application..." -ForegroundColor Blue
        docker-compose build --no-cache
    }
    "reset" {
        Write-Host "Resetting STMS application (removing volumes)..." -ForegroundColor Magenta
        docker-compose down -v
        docker-compose up -d
    }
    "shell" {
        if ($Service) {
            docker-compose exec $Service /bin/bash
        } else {
            docker-compose exec app /bin/bash
        }
    }
    default {
        Write-Host "STMS Docker Management" -ForegroundColor White
        Write-Host "Usage: .\docker-manage.ps1 -Command <command> [-Service <service>]" -ForegroundColor White
        Write-Host ""
        Write-Host "Commands:" -ForegroundColor Yellow
        Write-Host "  start   - Start all services" -ForegroundColor White
        Write-Host "  stop    - Stop all services" -ForegroundColor White
        Write-Host "  restart - Restart all services" -ForegroundColor White
        Write-Host "  logs    - Show logs (optionally for specific service: app, db, redis)" -ForegroundColor White
        Write-Host "  status  - Show service status" -ForegroundColor White
        Write-Host "  build   - Rebuild the application image" -ForegroundColor White
        Write-Host "  reset   - Reset everything including database volumes" -ForegroundColor White
        Write-Host "  shell   - Open shell in container (default: app)" -ForegroundColor White
        Write-Host ""
        Write-Host "Examples:" -ForegroundColor Yellow
        Write-Host "  .\docker-manage.ps1 -Command start" -ForegroundColor Gray
        Write-Host "  .\docker-manage.ps1 -Command logs -Service app" -ForegroundColor Gray
        Write-Host "  .\docker-manage.ps1 -Command shell -Service db" -ForegroundColor Gray
    }
}
