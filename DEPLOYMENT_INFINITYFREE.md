# PAWrtner Deployment Checklist for InfinityFree

## Pre-Deployment (Local)

- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Update `.env` for production:
  ```env
  APP_ENV=production
  APP_DEBUG=false
  APP_KEY=base64:... (from php artisan key:generate --show)
  ```
- [ ] Test the app locally: `php artisan serve`
- [ ] Commit all changes to git: `git add . && git commit -m "Pre-deployment prep"`
- [ ] Push to GitHub: `git push origin main`

## Step 1: Prepare Deployment Package

### Option A: Create a ZIP file (Recommended)

```powershell
# Navigate to project
cd "C:\Users\Jed Chrixtian\Herd\pawrtner"

# Create deployment zip (excludes node_modules and unneeded files)
$exclude = @('node_modules', '.git', '.gitignore', 'tests', 'database/seeders', 'DEPLOYMENT_INFINITYFREE.md')
$files = Get-ChildItem -Recurse | Where-Object { -not ($exclude | Where-Object { $_.FullName -match $_ }) }
$files | Compress-Archive -DestinationPath pawrtner-deploy.zip
```

### Option B: Manual upload
- Use FileZilla or InfinityFree File Manager
- Upload all files (see folder structure below)

## Step 2: InfinityFree Setup

1. Log in to [infinityfree.net](https://infinityfree.net)
2. Go to **File Manager** or **FTP Accounts**
3. Create FTP credentials if needed
4. Note your FTP host, username, password

## Step 3: Upload Files

### Via FileZilla (FTP):
1. Open FileZilla
2. File → Site Manager → New Site
3. Enter FTP credentials from InfinityFree
4. Connect
5. Navigate to `public_html/` on remote
6. Upload **all files EXCEPT**:
   - `node_modules/`
   - `.git/`
   - `tests/`
   - `DEPLOYMENT_INFINITYFREE.md`

### Via File Manager (Web):
1. Open InfinityFree File Manager
2. Navigate to `public_html/`
3. Upload the `pawrtner-deploy.zip` file
4. Extract it using File Manager's extract tool

### Folder Structure After Upload:
```
public_html/
├── app/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/              ← Upload contents of this folder here
│   ├── index.php        ← This should be in public_html root
│   ├── css/
│   ├── images/
│   └── js/
├── resources/
├── routes/
├── storage/             ← Must be writable (755)
├── vendor/
├── .env                 ← Create this
├── artisan
├── composer.json
└── ... (other files)
```

### Special Step: Public Folder Contents

Since InfinityFree serves from `public_html/`, you need to:

**Option 1: Extract public contents to root**
- Upload everything from `public/` folder directly to `public_html/`
- Your `index.php` should be in `public_html/`, not in `public_html/public/`
- Modify `index.php` to adjust paths:

```php
require __DIR__.'/../bootstrap/app.php';
// Change to:
require __DIR__.'/bootstrap/app.php';
```

**Option 2: Use subdirectory**
- Keep folder structure as `public_html/pawrtner/`
- Update domain routing to point to `public_html/pawrtner/public/`
- Contact InfinityFree support if unsure

## Step 4: Create & Configure Database

1. In InfinityFree control panel, go to **MySQL Databases**
2. Create a new database
3. Note:
   - Database name
   - Username
   - Password
   - Hostname (usually `localhost`)

## Step 5: Configure .env on Server

1. Open **File Manager** → `public_html/.env`
2. Edit with the following values:

```env
APP_NAME="PAWrtner"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_URL=https://yourdomain.infinityfreeapp.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

QUEUE_CONNECTION=sync
CACHE_DRIVER=file
SESSION_DRIVER=file
```

3. Save the file

## Step 6: Set Folder Permissions

1. Open **File Manager**
2. Right-click on `storage/` → **Properties** → Set permission to `755`
3. Right-click on `bootstrap/cache/` → **Properties** → Set permission to `755`
4. Right-click on `.env` → **Properties** → Set permission to `644`

## Step 7: Run Migrations

### Option A: Via SSH (if available)
```bash
ssh your_ssh_user@your_host
cd public_html
php artisan migrate --force
php artisan filament:upgrade
```

### Option B: Via one-time migration route
1. Edit `routes/web.php`:

```php
Route::get('/setup', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('filament:upgrade');
        return 'Migrations completed!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
```

2. Visit `https://yourdomain.infinityfreeapp.com/setup` once
3. Check if you see "Migrations completed!"
4. **Delete this route from `routes/web.php` after running**
5. Re-upload the cleaned `routes/web.php`

## Step 8: Verify Installation

- [ ] Visit `https://yourdomain.infinityfreeapp.com/admin/login`
- [ ] Visit `https://yourdomain.infinityfreeapp.com/vet/login`
- [ ] Visit `https://yourdomain.infinityfreeapp.com/client/login`
- [ ] Try logging in with a test account
- [ ] Check if logo + "PAWrtner" text appears correctly

## Troubleshooting

### 500 Internal Server Error
- Check `storage/logs/laravel.log` for errors
- Verify `.env` settings match InfinityFree database
- Ensure `storage/` and `bootstrap/cache/` are writable

### Database Connection Error
- Verify DB credentials in `.env` match InfinityFree
- Check database hostname (usually `localhost`, not a URL)
- Ensure database is created in InfinityFree control panel

### Logo Not Showing
- Check image path: `/images/PAWrtner_Logo.png` must exist in `public_html/images/`
- Verify CSS file loaded: check `public/css/pawrtner-brand.css` exists

### Routes Not Found (404)
- Run `php artisan route:cache` locally before uploading
- Delete `bootstrap/cache/routes.php` on server if cached routes are stale
- Contact InfinityFree if mod_rewrite isn't enabled

### Composer Dependencies Missing
- Ensure `vendor/` folder was uploaded completely
- Re-run `composer install` locally and re-upload `vendor/`

## InfinityFree Limitations to Remember

- ❌ **No background jobs** → Set `QUEUE_CONNECTION=sync` in `.env`
- ❌ **No cron jobs** → Scheduled tasks (reminders) won't auto-run
- ⚠️ **Limited PHP execution time** → Keep migrations/seeds lightweight
- ✅ **Filament admin panel** → Works perfectly
- ✅ **Database queries** → Full MySQL support
- ✅ **File uploads** → Works (use `storage/app/` or `public/uploads/`)

## Post-Deployment

- [ ] Test all three panels (Admin, Vet, Client)
- [ ] Test login/logout
- [ ] Verify database connectivity
- [ ] Check error logs: `storage/logs/laravel.log`
- [ ] Monitor uptime for first 24 hours

## Rollback Plan

If something goes wrong:
1. Keep a backup of your last working version
2. Use Git to revert: `git reset --hard HEAD~1`
3. Re-upload the previous version to InfinityFree
4. Contact InfinityFree support if server issues persist

---

**Need help?** Check [Laravel Deployment Docs](https://laravel.com/docs/deployment) or [InfinityFree Forum](https://forum.infinityfree.net)
