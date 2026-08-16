<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-13.8-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel Version"></a>
<a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php" alt="PHP Version"></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-green?style=for-the-badge" alt="License"></a>
</p>

# Laravel E-Commerce Platform

A modern, full-featured e-commerce API built with Laravel 13. This application provides a complete backend solution for online retail operations with role-based access control, product management, shopping cart functionality, and order processing.

## Table of Contents

- [Features](#features)
- [Technology Stack](#technology-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Project](#running-the-project)
- [Project Structure](#project-structure)
- [API Endpoints](#api-endpoints)
- [Database Schema](#database-schema)
- [Authentication](#authentication)
- [User Roles & Permissions](#user-roles--permissions)
- [Testing](#testing)
- [Development](#development)
- [Contributing](#contributing)
- [License](#license)

## Features

- 🔐 **Authentication & Authorization**: Laravel Sanctum for API token-based authentication
- 👥 **Role-Based Access Control**: Three user roles (Customer, Admin, Salesman) with specific permissions
- 🛍️ **Product Management**: Full CRUD operations for products with stock tracking
- 🛒 **Shopping Cart**: Add, update, remove items from cart with persistence
- 📦 **Order Management**: Place orders, track order status with multiple states (pending, processing, completed, cancelled)
- 📊 **Inventory Management**: Real-time stock quantity tracking
- 🔍 **Email Confirmation**: Email verification system for user registration
- 🎨 **Modern Frontend**: Built with Tailwind CSS and Vite
- 🧪 **Comprehensive Testing**: Unit and feature tests with Pest PHP
- 📝 **API Documentation**: Auto-generated API documentation with Scramble

## Technology Stack

- **Backend**: Laravel 13.8
- **PHP**: 8.3+
- **Database**: MySQL/PostgreSQL (configurable)
- **Authentication**: Laravel Sanctum 4.0
- **Testing**: Pest PHP 5.1
- **Frontend Build**: Vite with Tailwind CSS 4.0
- **API Documentation**: Dedoc Scramble
- **Code Formatting**: Laravel Pint
- **Faker**: FakerPHP for database seeding

## Requirements

Before you begin, ensure you have the following installed:

- **PHP 8.3+** with extensions: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- **Composer** (latest version)
- **Node.js 16+** and npm
- **MySQL 8.0+** or **PostgreSQL 12+**
- **Git**

## Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/Asemgado/Laravel-E-Commerce.git
cd Laravel-E-Commerce
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Setup Environment File

Copy the example environment file and generate an application key:

```bash
cp .env.example .env
php artisan key:generate
```

### Step 4: Configure Your Database

Edit the `.env` file and update the database connection settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_ecommerce
DB_USERNAME=root
DB_PASSWORD=
```

Create your database:

```bash
mysql -u root -p -e "CREATE DATABASE laravel_ecommerce;"
```

### Step 5: Run Database Migrations

```bash
php artisan migrate
```

### Step 6: Seed Sample Data (Optional)

```bash
php artisan db:seed
```

## Configuration

### Application Configuration

Key configuration files are located in the `config/` directory:

- **config/app.php**: Application name, timezone, and locale
- **config/auth.php**: Authentication configuration
- **config/database.php**: Database connection settings
- **config/mail.php**: Mail server configuration (for email verification)
- **config/sanctum.php**: API token authentication settings

### Environment Variables

Important environment variables to configure:

```env
# Application
APP_NAME="Laravel E-Commerce"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_ecommerce
DB_USERNAME=root
DB_PASSWORD=

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
```

## Running the Project

### Development Server

Start the Laravel development server:

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`
```


### Automated Setup

Run all setup commands in one go:

```bash
composer run setup
```

This will:

1. Install PHP dependencies
2. Copy `.env.example` to `.env`
3. Generate application key
4. Run database migrations
5. Install npm dependencies
6. Build frontend assets

## Project Structure

```
Laravel-E-Commerce/
├── app/
│   ├── Enums/                      # Application enums
│   │   ├── OrderStatus.php         # Order status states
│   │   └── UserRolesEnum.php       # User role definitions
│   ├── Http/
│   │   ├── Controllers/            # API controllers
│   │   ├── Middleware/             # Custom middleware
│   │   └── Requests/               # Form request validation
│   ├── Models/                     # Eloquent models
│   │   ├── Cart.php
│   │   ├── CartItem.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Product.php
│   │   └── User.php
│   ├── Services/                   # Business logic services
│   │   ├── CartService.php
│   │   ├── OrderService.php
│   │   └── ProductService.php
│   └── Providers/
│       └── AppServiceProvider.php  # Service provider configuration
├── database/
│   ├── factories/                  # Model factories for testing
│   ├── migrations/                 # Database migrations
│   └── seeders/                    # Database seeders
├── routes/
│   ├── api.php                     # API routes
│   └── web.php                     # Web routes
├── resources/
│   ├── css/                        # Tailwind CSS files
│   └── js/                         # JavaScript files
├── tests/
│   ├── Feature/                    # Feature tests
│   ├── Unit/                       # Unit tests
│   └── Pest.php                    # Pest configuration
├── config/                         # Configuration files
├── storage/                        # Logs, cache, and temporary files
├── public/                         # Public assets
├── bootstrap/                      # Application bootstrap files
├── .env.example                    # Example environment file
├── composer.json                   # PHP dependencies
├── package.json                    # JavaScript dependencies
├── phpunit.xml                     # PHPUnit configuration
├── vite.config.js                  # Vite configuration
└── artisan                         # Laravel command-line tool
```

## API Endpoints

### Authentication Endpoints

| Method | Endpoint | Description | Authentication |
| -------- | ---------- | ------------- | ----------------- |
| POST | `/api/register` | Register a new user | None |
| POST | `/api/login` | Login user | None |
| POST | `/api/logout` | Logout user | Sanctum |
| GET | `/api/user` | Get authenticated user info | Sanctum |
| GET | `/api/confirm-email` | Confirm user email | Admin |

### Product Endpoints

| Method | Endpoint | Description | Role |
| -------- | ---------- | ------------- | ------ |
| GET | `/api/products` | List all products | Customer, Salesman, Admin |
| GET | `/api/products/{id}` | Get product details | Customer, Salesman, Admin |
| POST | `/api/products` | Create new product | Salesman, Admin |
| PUT | `/api/products/{id}` | Update product | Salesman, Admin |
| DELETE | `/api/products/{id}` | Delete product | Salesman, Admin |

### Cart Endpoints

| Method | Endpoint | Description | Role |
| -------- | ---------- | ------------- | ------ |
| GET | `/api/cart` | Get user's cart | Customer, Admin |
| POST | `/api/cart` | Add item to cart | Customer, Admin |
| PATCH | `/api/cart/{productId}` | Update cart item quantity | Customer, Admin |
| DELETE | `/api/cart/{productId}` | Remove item from cart | Customer, Admin |
| DELETE | `/api/cart` | Clear entire cart | Customer, Admin |

### Order Endpoints

| Method | Endpoint | Description | Role |
| -------- | ---------- | ------------- | ------ |
| POST | `/api/orders` | Place a new order | Customer, Admin |
| GET | `/api/orders` | List user's orders | Customer, Admin |
| GET | `/api/orders/{id}` | Get order details | Customer, Admin |
| PATCH | `/api/orders/{id}/status/{status}` | Update order status | Salesman, Admin |


## Database Schema

### Users Table

- id (Primary Key)
- name
- email (Unique)
- password (Hashed)
- role (customer, admin, salesman)
- email_verified_at
- timestamps

### Products Table

- id (Primary Key)
- name
- description
- price (Decimal 8,2)
- stock_quantity
- user_id (Foreign Key - Salesman/Admin)
- timestamps

### Carts Table

- id (Primary Key)
- user_id (Foreign Key - Customer)
- timestamps

### Cart Items Table

- id (Primary Key)
- cart_id (Foreign Key)
- product_id (Foreign Key)
- quantity
- timestamps

### Orders Table

- id (Primary Key)
- user_id (Foreign Key - Customer)
- status (pending, processing, completed, cancelled)
- total_amount (Decimal 10,2)
- timestamps

### Order Items Table

- id (Primary Key)
- order_id (Foreign Key)
- product_id (Foreign Key)
- quantity
- unit_price (Decimal 8,2)
- timestamps

## Authentication

This application uses **Laravel Sanctum** for API authentication. After logging in, users receive an access token that must be included in subsequent requests.

### Login Example

**Request:**

```bash
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

**Response:**

```json
{
  "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "role": "customer"
  }
}
```

### Using the Token

Include the token in the Authorization header:

```bash
GET /api/cart
Authorization: Bearer 1|AbCdEfGhIjKlMnOpQrStUvWxYz...
Content-Type: application/json
```

## User Roles & Permissions

### Customer

- View products
- Manage personal cart
- Place and track orders
- View own order history

### Salesman

- View products
- Create, update, and delete products
- Update order status
- Manage their own product inventory

### Admin

- Full access to all endpoints
- Manage users (including email confirmation)
- Create, update, and delete products
- Update order status
- Access all orders and user data


## Development

### Running Commands

Use the Artisan CLI for various tasks:

```bash
# Create a new model with migration
php artisan make:model ModelName -m

# Create a new controller
php artisan make:controller ControllerName

# Create a new service class
php artisan make:class Services/ServiceName

# Create a new request class
php artisan make:request StoreProductRequest

# Run specific migrations
php artisan migrate:refresh --seed

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Database Seeding

Create and populate sample data:

```bash
php artisan db:seed
```

Reset database and seed:

```bash
php artisan migrate:fresh --seed
```

### Using Tinker

Interact with your application via REPL:

```bash
php artisan tinker

# Example commands in Tinker
> User::count()
> Product::with('user')->get()
> User::factory()->create()
```

### Logs

View real-time logs:

```bash
php artisan pail
```

## API Documentation

Auto-generated API documentation is available at `/api/documentation` when the server is running. This is powered by Dedoc Scramble.

## Common Issues & Troubleshooting

### "Database connection refused"

- Ensure MySQL/PostgreSQL is running
- Verify database credentials in `.env`
- Check DB_HOST is correct (usually `localhost` or `127.0.0.1`)

### "No application encryption key has been specified"

- Run: `php artisan key:generate`

### "Class not found" errors

- Run: `composer dumpautoload`

### "npm command not found"

- Ensure Node.js is installed: `node --version`
- Install npm packages: `npm install`

### "Migration table not found"

- Run: `php artisan migrate`
- Check database connection settings

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Commit your changes: `git commit -m 'Add amazing feature'`
4. Push to the branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

Please ensure:

- Your code follows Laravel conventions
- Tests are written for new features
- Code passes `composer pint` formatting
- All tests pass: `php artisan test`

## License

This Laravel E-Commerce platform is open-sourced software licensed under the [MIT license](LICENSE).
