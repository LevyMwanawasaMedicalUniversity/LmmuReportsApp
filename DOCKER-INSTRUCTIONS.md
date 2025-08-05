# Docker Deployment Instructions for LMMU Reports App

This document provides instructions on how to deploy the LMMU Reports App using Docker.

## Prerequisites

- Docker and Docker Compose installed on your server
- Basic knowledge of Docker and command-line operations

## Quick Start

1. Copy your `.env` file with proper database credentials:

```bash
cp .env.example .env
```

2. Edit the `.env` file and set appropriate values for:
   - `DB_HOST=db` (This should be 'db' to match the service name in docker-compose.yml)
   - `DB_DATABASE`
   - `DB_USERNAME`
   - `DB_PASSWORD`

3. Build and start the Docker containers:

```bash
docker-compose up -d
```

4. Generate Laravel application key:

```bash
docker-compose exec app php artisan key:generate
```

5. Run database migrations:

```bash
docker-compose exec app php artisan migrate
```

6. Set proper permissions:

```bash
docker-compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
```

7. Access your application at: `http://localhost:8000`

## Container Information

This setup includes the following containers:

- **app**: PHP 8.2 FPM with all required extensions (zip, gd, curl) installed
- **webserver**: Nginx web server
- **db**: MySQL 8.0 database server

## Common Commands

- Start containers: `docker-compose up -d`
- Stop containers: `docker-compose down`
- View logs: `docker-compose logs -f`
- Access PHP container shell: `docker-compose exec app bash`
- Run Artisan commands: `docker-compose exec app php artisan <command>`

## Production Deployment Notes

For production deployment, consider the following adjustments:

1. Update the `.env` file with production settings:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - Strong passwords for database

2. Configure proper SSL in the Nginx container by adding a certificate volume and updating the Nginx configuration.

3. Consider using a separate database server for production instead of a containerized database.

## Troubleshooting

### Container Won't Start

Check logs with: `docker-compose logs -f [service_name]`

### PHP Extension Issues

The Dockerfile is configured to install all required PHP extensions including:
- zip
- gd
- curl

If additional extensions are needed, modify the Dockerfile and rebuild:

```bash
docker-compose build --no-cache
docker-compose up -d
```

### Database Connection Issues

Ensure your `.env` file has `DB_HOST=db` to properly connect to the containerized MySQL service.
