#!/bin/bash

# STMS Docker Management Script

case "$1" in
    "start")
        echo "Starting STMS application with database..."
        docker-compose up -d
        echo "Waiting for services to be ready..."
        sleep 10
        docker-compose ps
        echo ""
        echo "Application should be available at: http://localhost:8080"
        echo "Database: localhost:3306 (user: stms_user, password: stms_password)"
        ;;
    "stop")
        echo "Stopping STMS application..."
        docker-compose down
        ;;
    "restart")
        echo "Restarting STMS application..."
        docker-compose down
        docker-compose up -d
        ;;
    "logs")
        if [ -n "$2" ]; then
            docker-compose logs -f "$2"
        else
            docker-compose logs -f
        fi
        ;;
    "status")
        docker-compose ps
        ;;
    "build")
        echo "Building STMS application..."
        docker-compose build --no-cache
        ;;
    "reset")
        echo "Resetting STMS application (removing volumes)..."
        docker-compose down -v
        docker-compose up -d
        ;;
    "shell")
        if [ -n "$2" ]; then
            docker-compose exec "$2" /bin/bash
        else
            docker-compose exec app /bin/bash
        fi
        ;;
    *)
        echo "STMS Docker Management"
        echo "Usage: $0 {start|stop|restart|logs [service]|status|build|reset|shell [service]}"
        echo ""
        echo "Commands:"
        echo "  start   - Start all services"
        echo "  stop    - Stop all services"
        echo "  restart - Restart all services"
        echo "  logs    - Show logs (optionally for specific service: app, db, redis)"
        echo "  status  - Show service status"
        echo "  build   - Rebuild the application image"
        echo "  reset   - Reset everything including database volumes"
        echo "  shell   - Open shell in container (default: app)"
        ;;
esac
