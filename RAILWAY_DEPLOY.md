# 🚂 Deploy Laravel ke Railway

## 📋 Prerequisites
- GitHub account
- Railway account (https://railway.app)
- Project Laravel sudah di GitHub

## 🚀 Langkah Deployment

### 1. Push ke GitHub
```bash
git add .
git commit -m "Setup Railway deployment"
git push origin main
```

### 2. Login ke Railway
- Buka https://railway.app
- Login dengan GitHub
- Klik "New Project"

### 3. Deploy dari GitHub
- Pilih "Deploy from GitHub repo"
- Pilih repository project
- Railway akan auto-detect Laravel

### 4. Setup Database
- Klik "New" → "Database" → "MySQL"
- Railway akan buat database otomatis
- Copy connection details

### 5. Setup Environment Variables
Di Railway dashboard, tambahkan:
```
APP_NAME=Gameta
APP_ENV=production
APP_KEY=base64:... (generate dengan php artisan key:generate)
APP_DEBUG=false
APP_URL=https://your-app.railway.app

DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

### 6. Deploy
- Railway akan auto-deploy setiap push ke GitHub
- Tunggu build selesai
- Cek logs jika ada error

### 7. Setup Domain (Optional)
- Klik "Settings" → "Domains"
- Tambahkan custom domain
- Railway akan handle SSL otomatis

## 🔧 Troubleshooting

### Error: "No application keys set"
```bash
# Generate APP_KEY
php artisan key:generate
# Copy key ke Railway environment variables
```

### Error: Database connection
- Pastikan database sudah dibuat
- Cek credentials di Railway
- Pastikan database accessible

### Error: Storage permissions
```bash
# Di local, jalankan:
php artisan storage:link
# Commit dan push ke GitHub
```

## 📱 Fitur Railway

✅ **Auto-deploy** dari GitHub
✅ **Database MySQL** otomatis
✅ **Custom domain** gratis
✅ **SSL certificate** otomatis
✅ **Environment variables** management
✅ **Logs** real-time
✅ **Metrics** dan monitoring
✅ **Rollback** ke versi sebelumnya

## 🌐 Akses Demo

Setelah deploy berhasil:
- **URL Demo**: https://your-app.railway.app
- **Admin Panel**: https://your-app.railway.app/admin
- **API Docs**: https://your-app.railway.app/api

## 💰 Pricing

- **Free Tier**: $5 credit/bulan
- **Pro**: $20/bulan
- **Team**: $20/user/bulan

Untuk demo project, Free Tier sudah cukup!
