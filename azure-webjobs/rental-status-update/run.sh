#!/bin/bash

# Azure WebJob for updating rental status
# This script runs the Laravel command to update expired rental statuses

cd /home/site/wwwroot

# Run the rental status update command
php artisan rentals:update-status

# Log the execution
echo "$(date): Rental status update completed" >> /home/site/wwwroot/storage/logs/webjobs.log 