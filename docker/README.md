# Docker Configuration

This directory contains Docker-related configuration files for the Laravel Sail development environment.

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