# ============================================================================
# DARUL ARQAM PORTAL - PERFORMANCE OPTIMIZATION QUICK DEPLOY SCRIPT
# ============================================================================
# Windows PowerShell Version
# Run: .\performance-optimize.ps1
# ============================================================================

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "DARUL ARQAM PERFORMANCE OPTIMIZATION" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Run Database Migration
Write-Host "Step 1: Adding performance indexes..." -ForegroundColor Yellow
php artisan migrate --path=database/migrations/2026_05_04_000000_add_performance_indexes.php
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Database migration completed" -ForegroundColor Green
} else {
    Write-Host "❌ Database migration failed" -ForegroundColor Red
    exit 1
}
Write-Host ""

# Step 2: Clear all caches
Write-Host "Step 2: Clearing caches..." -ForegroundColor Yellow
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
Write-Host "✅ Caches cleared" -ForegroundColor Green
Write-Host ""

# Step 3: Update controller
Write-Host "Step 3: Updating StudentPortalController..." -ForegroundColor Yellow
Write-Host "Please manually replace app/Http/Controllers/StudentPortalController.php" -ForegroundColor Cyan
Write-Host "with the optimized version from StudentPortalControllerOptimized.php" -ForegroundColor Cyan
Write-Host ""

# Step 4: Build frontend assets (optional)
Write-Host "Step 4: Building frontend assets..." -ForegroundColor Yellow
try {
    npm run build
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Frontend assets built" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Frontend build skipped" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠️  npm not available" -ForegroundColor Yellow
}
Write-Host ""

# Step 5: Update .env
Write-Host "Step 5: Update .env configuration" -ForegroundColor Yellow
Write-Host "Please update .env with:" -ForegroundColor Cyan
Write-Host "  CACHE_STORE=file   (development)" -ForegroundColor Cyan
Write-Host "  # OR for production:" -ForegroundColor Cyan
Write-Host "  CACHE_STORE=redis" -ForegroundColor Cyan
Write-Host ""

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "✅ OPTIMIZATION STEPS COMPLETED!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "1. Manually update .env file (CACHE_STORE setting)" -ForegroundColor White
Write-Host "2. Replace StudentPortalController with optimized version" -ForegroundColor White
Write-Host "3. Test dashboard page load time" -ForegroundColor White
Write-Host "4. Verify browser caching is working (DevTools > Network)" -ForegroundColor White
Write-Host ""
Write-Host "For detailed info, see: PERFORMANCE_OPTIMIZATION_GUIDE.md" -ForegroundColor Cyan
