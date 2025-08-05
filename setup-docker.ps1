# PowerShell script to set up and run LMMU Reports App in Docker
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

# Wait for containers to be fully up
Write-Host "Waiting for containers to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Install dependencies and setup Laravel
Write-Host "Setting up Laravel application..." -ForegroundColor Green
docker-compose exec app composer update --no-interaction --optimize-autoloader
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --force
docker-compose exec app sh -c "chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache"

# Optimize for production
Write-Host "Optimizing for production..." -ForegroundColor Green
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

Write-Host "LMMU Reports App is now running at http://localhost:8000" -ForegroundColor Green
Write-Host "Press any key to exit..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
