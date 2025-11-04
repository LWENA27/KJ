# Railway Deployment Guide for KJ Medical System

## ✅ Preparation Complete!

Your application is now configured for Railway deployment with automatic database migration.

---

## 🚀 Railway Deployment Steps

### Step 1: Create Railway Account
1. Go to: https://railway.app/
2. Click **"Start a New Project"**
3. Sign in with your **GitHub account** (recommended)
4. Verify your account (may need to add credit card for $5 free credits)

---

### Step 2: Deploy Your Application

1. **Click "New Project"** in Railway dashboard
2. Select **"Deploy from GitHub repo"**
3. Choose your repository: `LWENA27/KJ`
4. Railway will automatically detect it's a PHP project

---

### Step 3: Add MySQL Database

1. In your Railway project, click **"+ New"**
2. Select **"Database"** → **"Add MySQL"**
3. Railway will automatically provision a MySQL database
4. Copy the database credentials that appear

---

### Step 4: Configure Environment Variables

1. Click on your **web service** (not the database)
2. Go to **"Variables"** tab
3. Add these environment variables:

```
DB_HOST = (copy from MySQL service - looks like: containers-us-west-xxx.railway.app)
DB_NAME = railway
DB_USER = root
DB_PASS = (copy from MySQL service)
PORT = 8080
```

**How to get MySQL credentials:**
- Click on the **MySQL service** in your project
- Go to **"Connect"** tab
- Copy: `MYSQLHOST`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`

---

### Step 5: Deploy & Migrate

1. Railway will automatically deploy your code
2. The `railway-deploy.sh` script will run automatically:
   - ✅ Wait for MySQL to be ready
   - ✅ Create database if needed
   - ✅ Import `zahanati.sql` schema (if tables don't exist)
   - ✅ Run `add_diagnosis_columns.sql` migration
   - ✅ Set proper permissions

3. Watch the deployment logs:
   - Click **"Deployments"** tab
   - Click the latest deployment
   - Watch for: "✅ Deployment complete! Application is ready."

---

### Step 6: Access Your Application

1. Go to **"Settings"** tab in your web service
2. Scroll to **"Networking"**
3. Click **"Generate Domain"**
4. Your app will be available at: `https://your-app-name.up.railway.app`

---

## 🔄 Future Updates (Push to Deploy)

After initial setup, updates are automatic:

```bash
# Make changes to your code
git add .
git commit -m "Your update message"
git push origin main
```

Railway will automatically:
1. Detect the push
2. Deploy new code
3. Run migrations (if any new ones exist)
4. Restart the application

---

## 📊 What Gets Migrated Automatically

### On First Deploy (Empty Database):
- Complete `zahanati.sql` schema import
- All tables, indexes, foreign keys
- Sample data (if any)

### On Every Deploy:
- `add_diagnosis_columns.sql` (preliminary_diagnosis, final_diagnosis)
- Any new migration files you add to `database/` folder

---

## 🛠️ How to Add New Migrations

Create a new `.sql` file in `database/` folder, then add it to `railway-deploy.sh`:

```bash
if [ -f "database/your_new_migration.sql" ]; then
    echo "🔄 Running migration: your_new_migration.sql"
    mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < database/your_new_migration.sql 2>/dev/null || echo "Migration already applied"
fi
```

---

## 🔍 Monitoring & Logs

**View Logs:**
1. Railway Dashboard → Your Project
2. Click on web service
3. Go to **"Deployments"** → Click latest
4. See real-time logs

**Check Database:**
1. Click MySQL service
2. Go to **"Data"** tab
3. Run SQL queries directly

---

## 🆘 Troubleshooting

### Database Connection Failed
- Check environment variables are set correctly
- Verify MySQL service is running (green dot)
- Check `DB_HOST` doesn't have `mysql://` prefix (just the hostname)

### Migration Errors
- Check deployment logs for SQL errors
- Verify migration files have correct SQL syntax
- Check if columns/tables already exist (migrations are idempotent)

### Application Not Loading
- Check PHP syntax: `php -l filename.php`
- Verify all files are pushed to GitHub
- Check Railway logs for PHP errors

---

## 💡 Pro Tips

1. **Test locally first:**
   ```bash
   export DB_HOST=localhost
   export DB_NAME=zahanati
   export DB_USER=root
   export DB_PASS=
   php -S localhost:8000
   ```

2. **Check what's deployed:**
   - Railway shows exact commit hash deployed
   - Compare with: `git log --oneline -1`

3. **Database backups:**
   - Railway auto-backups on paid plans
   - Manual backup: Use Railway CLI or export from Data tab

4. **Use Railway CLI (optional):**
   ```bash
   npm i -g @railway/cli
   railway login
   railway link
   railway logs
   ```

---

## 🎓 GitHub Student Benefits

Your GitHub Student Pack includes:
- Railway credits
- DigitalOcean credits
- More hosting options

Activate at: https://education.github.com/pack

---

## ✅ Files Created for Railway

- ✅ `railway-deploy.sh` - Automatic database migration script
- ✅ `railway.toml` - Railway configuration
- ✅ `.railwayignore` - Files to exclude from deployment
- ✅ `config/database.php` - Updated to use environment variables
- ✅ `package.json` - Updated with deploy script

---

## 🚀 Ready to Deploy!

1. Commit these changes:
   ```bash
   git add .
   git commit -m "Configure Railway deployment with auto migrations"
   git push origin main
   ```

2. Follow Steps 1-6 above

3. Your app will be live with database automatically migrated!

---

**Need help?** Railway has excellent documentation: https://docs.railway.app/

Good luck! 🎉
