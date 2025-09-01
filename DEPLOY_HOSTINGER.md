# 🚀 Simple Hostinger Deployment Guide (First Time)

This guide is made super simple for your first deployment on Hostinger!

## 📋 What You Need Before Starting
- A Hostinger hosting plan (any plan with PHP 8.1+)
- Your domain name
- Basic computer skills (you can do this!)

## 🎯 Step-by-Step Deployment

### Step 1: Buy Hostinger Plan
1. Go to [hostinger.com](https://hostinger.com)
2. Choose any hosting plan (Premium or higher recommended)
3. Buy and wait for activation email

### Step 2: Prepare Your Files (Do This Once)
1. Open Command Prompt/Terminal in your project folder
2. Run these commands one by one:
   ```bash
   npm install
   npm run build
   ```
3. This creates a `public/build` folder (important!)

### Step 3: Upload to Hostinger
1. Login to Hostinger hPanel
2. Go to **File Manager**
3. Navigate to `public_html` folder
4. **Delete everything** in `public_html` (it's empty anyway)
5. **Upload your entire project folder** to `public_html`
6. **Important**: After upload, you'll see your project folder inside `public_html`. You need to move all the files FROM inside your project folder UP into `public_html` itself.

**What this means:**
- Upload your project folder (with all files inside) to `public_html`
- Then open your project folder in File Manager
- Select ALL files and folders inside it (app, bootstrap, config, database, etc.)
- Cut them and paste them directly into `public_html`
- Delete the now-empty project folder

**Final result:** Your `public_html` should contain: `app`, `bootstrap`, `config`, `database`, `public`, `resources`, `routes`, `storage`, `.env`, etc. - NOT a folder containing these.

### Step 4: Set Up Database
1. In hPanel, go to **Databases** → **MySQL Databases**
2. Create a new database (note the name)
3. Create a database user (note username and password)
4. Add user to database with **ALL PRIVILEGES**

**Important Notes:**
- **Don't import your local database** - you'll create a fresh one on Hostinger
- Laravel will create all the tables automatically when you run `php artisan migrate` later
- Your local database data won't be transferred (unless you specifically want to export/import it)
- The database connection is configured in the `.env` file you'll edit in Step 5

### Step 5: Configure Your App
1. In File Manager, find `.env.example` in your project
2. Right-click → **Rename** → change to `.env`
3. Right-click `.env` → **Edit**
4. Change these lines:
   ```
   APP_URL=https://yourdomain.com
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```
5. Save the file

### Step 6: Install Dependencies
1. In hPanel, go to **Advanced** → **SSH Access**
2. Enable SSH and note your SSH details
3. Use any SSH app (like PuTTY on Windows) to connect
4. Run these commands one by one:
   ```bash
   cd public_html
   composer install --no-dev
   php artisan key:generate
   php artisan migrate
   php artisan storage:link
   ```

### Step 7: Set Permissions
1. Still in SSH, run:
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

### Step 8: Test Your Site!
1. Visit your domain in browser
2. If it works - congratulations! 🎉
3. If it doesn't work, check the troubleshooting section below

## 🔧 If Something Goes Wrong

### Common Issues:
1. **White page**: Check if `.env` file exists and has correct database info
2. **Database error**: Verify database credentials in `.env`
3. **Permission error**: Make sure you ran the chmod commands
4. **Assets not loading**: Check if `public/build` folder exists

### Get Help:
- Check `storage/logs/laravel.log` for error messages
- Contact Hostinger support (they're very helpful!)
- Make sure PHP version is 8.1 or higher in hPanel → **Advanced** → **PHP Configuration**

## 💡 Pro Tips
- Take screenshots of your database settings
- Save your SSH password somewhere safe
- Don't panic - this is normal for first deployment!
- Hostinger support is excellent for beginners

## 🎉 You're Done!
Your Laravel app should now be live on your domain! 

**Need help?** Hostinger has 24/7 live chat support - they're great for beginners!
