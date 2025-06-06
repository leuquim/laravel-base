# Docker Configuration

This directory contains Docker-related configuration files for the Laravel Sail development environment.

## Services

The Docker Compose setup includes the following services:

- **laravel.test**: Main Laravel application server
- **horizon**: Laravel Horizon queue worker service
- **redis**: Redis server for caching and queues
- **pgsql**: PostgreSQL database server

## Files

### php.ini
Custom PHP configuration file that overrides default settings:

- **Memory limit**: Increased to 512M (from default 128M) to support PHPStan and other development tools
- **Execution time**: Increased to 300 seconds for long-running scripts
- **Upload limits**: Set to 64M for file uploads
- **Error reporting**: Enabled for development
- **OPcache**: Configured for development with CLI support
- **Performance**: Optimized realpath cache and input variable limits

The file is mounted to both CLI and FPM PHP configurations in the container:
- `/etc/php/8.4/cli/conf.d/99-custom.ini`
- `/etc/php/8.4/fpm/conf.d/99-custom.ini`

## Usage

After making changes to `php.ini`, restart the containers:

```bash
./vendor/bin/sail down
./vendor/bin/sail up -d
```

To verify the configuration is loaded:

```bash
./vendor/bin/sail php -i | grep memory_limit
```

## Laravel Horizon

Laravel Horizon is configured to run as a separate Docker service for queue processing.

### Accessing Horizon Dashboard

Once the containers are running, you can access the Horizon dashboard at:
- http://localhost/horizon

### Testing Horizon

To test that Horizon is working properly:

1. Visit http://localhost/test-horizon to dispatch test jobs
2. Check the Horizon dashboard to see the jobs being processed
3. View logs with: `./vendor/bin/sail logs horizon`

### Horizon Configuration

- Configuration file: `config/horizon.php`
- Queue connection: Redis (configured in `.env`)
- Local environment: 3 max processes
- Metrics snapshots: Scheduled every 5 minutes

### Managing Horizon

```bash
# View Horizon logs
./vendor/bin/sail logs horizon

# Restart Horizon service
./vendor/bin/sail restart horizon

# Stop/Start specific services
./vendor/bin/sail stop horizon
./vendor/bin/sail start horizon
```