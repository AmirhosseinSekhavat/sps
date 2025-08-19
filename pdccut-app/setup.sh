#!/bin/bash

echo "🚀 Setting up PDCCUT.IR Application..."

# Check if .env exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
    
    # Update .env with correct settings
    sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
    sed -i 's/APP_LOCALE=en/APP_LOCALE=fa/' .env
    sed -i 's/APP_FALLBACK_LOCALE=en/APP_FALLBACK_LOCALE=fa/' .env
    sed -i 's/APP_FAKER_LOCALE=en_US/APP_FAKER_LOCALE=fa_IR/' .env
    sed -i 's/TIMEZONE=UTC/TIMEZONE=Asia\/Tehran/' .env
    sed -i 's/QUEUE_CONNECTION=sync/QUEUE_CONNECTION=database/' .env
    sed -i 's/SESSION_DRIVER=file/SESSION_DRIVER=database/' .env
fi

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-interaction

echo "📦 Installing Node.js dependencies..."
npm install

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Create SQLite database
echo "🗄️ Creating SQLite database..."
touch database/database.sqlite

# Run migrations
echo "🔄 Running database migrations..."
php artisan migrate --force

# Create storage links
echo "🔗 Creating storage links..."
php artisan storage:link

# Clear caches
echo "🧹 Clearing application caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Create admin user
echo "👤 Creating admin user..."
echo "Please run: php artisan make:filament-user"

# Build assets
echo "🏗️ Building frontend assets..."
npm run build

echo "✅ Setup completed successfully!"
echo ""
echo "🎯 Next steps:"
echo "1. Run: php artisan make:filament-user"
echo "2. Start server: php artisan serve"
echo "3. Access admin panel: http://localhost:8000/admin"
echo "4. Access user login: http://localhost:8000/auth/login"
echo ""
echo "📚 For more information, check README.md" 