#!/bin/bash
# This script works in both Linux bash and Windows PowerShell
# PowerShell will ignore the lines starting with #
# PowerShell will treat the : as a label and $() as a subexpression
: '
@echo off
echo "Windows environment detected"
goto :windows
'

# Linux section
echo "Linux environment detected"
echo "Setting up LMMU Reports App in Docker..."

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Prepare .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example"
    cp .env.example .env
    
    # Update database settings
    sed -i 's/DB_HOST=127.0.0.1/DB_HOST=db/g' .env
    echo "Please edit your .env file to set database credentials"
fi

# Build and start containers
echo "Building and starting Docker containers..."
docker-compose up -d

# Install dependencies and setup Laravel
echo "Setting up Laravel application..."
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Optimize for production
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo "LMMU Reports App is now running at http://localhost:8000"
exit 0

# Windows PowerShell section
:windows
powershell -Command {
    Write-Host "Setting up LMMU Reports App in Docker..." -ForegroundColor Green

    # Check if Docker is installed
    if (!(Get-Command docker -ErrorAction SilentlyContinue)) {
        Write-Host "Docker is not installed. Please install Docker Desktop for Windows first." -ForegroundColor Red
        Exit 1
    }

    # Check if Docker Compose is installed
    if (!(Get-Command docker-compose -ErrorAction SilentlyContinue)) {
        Write-Host "Docker Compose is not installed. Please install Docker Desktop for Windows first." -ForegroundColor Red
        Exit 1
    }

    # Prepare .env file if it doesn't exist
    if (!(Test-Path .env)) {
        Write-Host "Creating .env file from .env.example" -ForegroundColor Yellow
        Copy-Item .env.example .env
        
        # Update database settings
        (Get-Content .env) -replace 'DB_HOST=127.0.0.1', 'DB_HOST=db' | Set-Content .env
        Write-Host "Please edit your .env file to set database credentials" -ForegroundColor Yellow
    }

    # Build and start containers
    Write-Host "Building and starting Docker containers..." -ForegroundColor Green
    docker-compose up -d

    # Install dependencies and setup Laravel
    Write-Host "Setting up Laravel application..." -ForegroundColor Green
    docker-compose exec app composer install
    docker-compose exec app php artisan key:generate
    docker-compose exec app php artisan migrate
    docker-compose exec app "chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache"

    # Optimize for production
    docker-compose exec app php artisan config:cache
    docker-compose exec app php artisan route:cache
    docker-compose exec app php artisan view:cache

    Write-Host "LMMU Reports App is now running at http://localhost:8000" -ForegroundColor Green
}
