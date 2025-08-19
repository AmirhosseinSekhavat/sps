.PHONY: help install setup serve test clean migrate fresh seed

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Install all dependencies
	composer install
	npm install

setup: ## Initial setup of the application
	@echo "🚀 Setting up PDCCUT.IR Application..."
	@if [ ! -f .env ]; then cp .env.example .env; fi
	@sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
	@sed -i 's/APP_LOCALE=en/APP_LOCALE=fa/' .env
	@sed -i 's/APP_FALLBACK_LOCALE=en/APP_FALLBACK_LOCALE=fa/' .env
	@sed -i 's/APP_FAKER_LOCALE=en_US/APP_FAKER_LOCALE=fa_IR/' .env
	@sed -i 's/TIMEZONE=UTC/TIMEZONE=Asia\/Tehran/' .env
	@sed -i 's/QUEUE_CONNECTION=sync/QUEUE_CONNECTION=database/' .env
	@sed -i 's/SESSION_DRIVER=file/SESSION_DRIVER=database/' .env
	@php artisan key:generate
	@touch database/database.sqlite
	@php artisan migrate --force
	@php artisan storage:link
	@make clean
	@npm run build
	@echo "✅ Setup completed successfully!"

serve: ## Start the development server
	php artisan serve --host=0.0.0.0 --port=8000

dev: ## Start development with Vite
	npm run dev

build: ## Build frontend assets
	npm run build

test: ## Run tests
	php artisan test

clean: ## Clear all caches
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear
	php artisan cache:clear

migrate: ## Run database migrations
	php artisan migrate

fresh: ## Fresh database migration
	php artisan migrate:fresh

seed: ## Seed the database
	php artisan db:seed

admin: ## Create admin user
	php artisan make:filament-user

queue: ## Start queue worker
	php artisan queue:work

sms-test: ## Test SMS service
	@read -p "Enter mobile number: " mobile; \
	read -p "Enter message: " message; \
	php artisan sms:test $$mobile "$$message"

docker-up: ## Start Docker services
	docker-compose up -d

docker-down: ## Stop Docker services
	docker-compose down

docker-logs: ## Show Docker logs
	docker-compose logs -f

status: ## Show application status
	@echo "📊 Application Status:"
	@echo "PHP Version: $$(php -v | head -n1)"
	@echo "Laravel Version: $$(php artisan --version)"
	@echo "Database: $$(php artisan tinker --execute='echo config(\"database.default\")')"
	@echo "Queue: $$(php artisan tinker --execute='echo config(\"queue.default\")')"
	@echo "Storage: $$(ls -la storage/ | head -n5)" 