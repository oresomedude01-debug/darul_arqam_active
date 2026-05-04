# DARUL ARQAM PORTAL - PERFORMANCE OPTIMIZED .ENV CONFIGURATION
# 
# Copy the relevant sections below and update your .env file
# Created: May 4, 2026

# ============================================================================
# CACHE CONFIGURATION - CRITICAL FOR PERFORMANCE
# ============================================================================

# Development Environment (Use File Cache)
CACHE_STORE=file
# CACHE_STORE=database        # ❌ DELETE THIS LINE - SLOW!

# Production Environment (Use Redis - Recommended)
# CACHE_STORE=redis
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379

# ============================================================================
# APPLICATION ENVIRONMENT - PRODUCTION SETTINGS
# ============================================================================

APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your_app_key_here

# ============================================================================
# DATABASE QUERY LOGGING (Optional - for monitoring)
# ============================================================================

# Enable slow query logging (logs queries > 100ms)
DB_LOG_QUERIES=false
DB_LOG_QUERIES_SLOW_MS=100

# ============================================================================
# SESSION CONFIGURATION
# ============================================================================

SESSION_DRIVER=cookie
SESSION_LIFETIME=120

# ============================================================================
# MAIL CONFIGURATION (Queue jobs for better performance)
# ============================================================================

MAIL_DRIVER=smtp
# Consider using queue for sending mails asynchronously
QUEUE_CONNECTION=database  # or 'redis' for better performance

# ============================================================================
# QUEUE CONFIGURATION (For background jobs)
# ============================================================================

# Recommended for production:
# QUEUE_CONNECTION=redis
# QUEUE_CONNECTION=beanstalkd

# ============================================================================
# LOGGING
# ============================================================================

LOG_CHANNEL=stack
LOG_LEVEL=info  # Use 'warning' or 'error' in production for less I/O

# ============================================================================
# PERFORMANCE MONITORING (Optional)
# ============================================================================

# Enable Laravel Debugbar only in development
# DEBUGBAR_ENABLED=false  # Disable in production

# ============================================================================
# API THROTTLING (Optional)
# ============================================================================

API_THROTTLE_REQUESTS=60
API_THROTTLE_MINUTES=1

# ============================================================================
# FULL RECOMMENDED .ENV SECTION FOR PERFORMANCE
# ============================================================================

# Just copy and paste from here:

APP_NAME="Darul Arqam"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=notice

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=darul_arqam
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_STORE=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=cookie
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@darul-arqam.local
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_URLS=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

# ============================================================================
# PERFORMANCE MONITORING
# ============================================================================

# Disable in production:
# - Debugbar
# - Query logging (unless investigating issues)
# - Query profiling

# Enable in production:
# - Redis caching
# - Database query caching
# - Static file browser caching (.htaccess)
# - Gzip compression

# ============================================================================
# DEPLOYMENT CHECKLIST
# ============================================================================

# Before deploying to production, ensure:
# [ ] APP_DEBUG=false
# [ ] APP_ENV=production
# [ ] CACHE_STORE=redis (or file with proper permissions)
# [ ] Queue jobs moved to background
# [ ] Database indexes created (run migration)
# [ ] .htaccess caching headers configured
# [ ] Gzip compression enabled
# [ ] Static assets versioned/minified
# [ ] SSL certificate configured (HTTPS)
# [ ] Monitoring/logging configured
