#!/bin/bash

# ============================================================================
# DARUL ARQAM PORTAL - PERFORMANCE OPTIMIZATION QUICK DEPLOY SCRIPT
# ============================================================================
# This script automates the performance optimization implementation
# Run: bash performance-optimize.sh
# ============================================================================

echo "=========================================="
echo "DARUL ARQAM PERFORMANCE OPTIMIZATION"
echo "=========================================="
echo ""

# Step 1: Run Database Migration
echo "Step 1: Adding performance indexes..."
php artisan migrate --path=database/migrations/2026_05_04_000000_add_performance_indexes.php
if [ $? -eq 0 ]; then
    echo "✅ Database migration completed"
else
    echo "❌ Database migration failed"
    exit 1
fi
echo ""

# Step 2: Clear all caches
echo "Step 2: Clearing caches..."
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Caches cleared"
echo ""

# Step 3: Update controller
echo "Step 3: Updating StudentPortalController..."
echo "Please manually replace app/Http/Controllers/StudentPortalController.php"
echo "with the optimized version from StudentPortalControllerOptimized.php"
echo "Or run: cp app/Http/Controllers/StudentPortalControllerOptimized.php app/Http/Controllers/StudentPortalController.php"
echo ""

# Step 4: Build frontend assets
echo "Step 4: Building frontend assets..."
npm run build
if [ $? -eq 0 ]; then
    echo "✅ Frontend assets built"
else
    echo "⚠️  Frontend build skipped (npm not available)"
fi
echo ""

# Step 5: Update .env
echo "Step 5: Update .env configuration"
echo "Please update .env with:"
echo "  CACHE_STORE=file   (development)"
echo "  # OR for production:"
echo "  CACHE_STORE=redis"
echo ""

echo "=========================================="
echo "✅ OPTIMIZATION STEPS COMPLETED!"
echo "=========================================="
echo ""
echo "Next Steps:"
echo "1. Manually update .env file (CACHE_STORE setting)"
echo "2. Replace StudentPortalController with optimized version"
echo "3. Test dashboard page load time"
echo "4. Verify browser caching is working (DevTools > Network)"
echo ""
echo "For detailed info, see: PERFORMANCE_OPTIMIZATION_GUIDE.md"
