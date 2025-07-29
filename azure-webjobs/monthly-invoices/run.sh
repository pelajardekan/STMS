#!/bin/bash

# Azure WebJob for generating monthly invoices
# This script runs the Laravel command to generate monthly invoices for active rentals

cd /home/site/wwwroot

# Run the monthly invoice generation command
php artisan invoices:generate-monthly

# Log the execution
echo "$(date): Monthly invoice generation completed" >> /home/site/wwwroot/storage/logs/webjobs.log 