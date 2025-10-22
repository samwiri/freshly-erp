<p align="center">
    <h1 align="center">LaundryMart ERP System</h1>
</p>

<p align="center">
    <img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
    <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
    <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
    <img src="https://img.shields.io/badge/Redis-6.0+-DC382D?style=for-the-badge&logo=redis&logoColor=white" alt="Redis">
</p>

## About LaundryMart ERP

LaundryMart ERP is a comprehensive Enterprise Resource Planning system designed specifically for laundry and dry-cleaning businesses. Built with Laravel 10.x, it provides a complete solution for managing all aspects of laundry operations.

### 🌟 Core Features

- **👥 Customer Management** - Complete customer profiles with loyalty program (Bronze/Silver/Gold/Platinum tiers)
- **📦 Order Processing** - Multi-stage workflow (received → washing → drying → ironing → ready → delivered)
- **💰 Financial Management** - Invoices, payments, expenses, and comprehensive reporting
- **👔 HR Management** - Employee attendance, payroll, and performance tracking
- **📊 Inventory Control** - Supplies and equipment tracking with automated alerts
- **📱 Multi-Channel Marketing** - Email, SMS, and push notification campaigns
- **🔔 Real-time Notifications** - Email, SMS, push, and in-app notifications
- **📈 Analytics & Reporting** - Dashboard with detailed business insights

### 🎯 Business Capabilities

- **Loyalty Program**: Tiered rewards system with automatic point tracking and redemption
- **Smart Pricing**: Tier-based discounts and dynamic pricing
- **Order Tracking**: Complete order lifecycle management with status history
- **Payment Processing**: Multiple payment methods with Stripe/PayPal integration
- **Inventory Alerts**: Automated low-stock notifications and reorder reminders
- **Equipment Maintenance**: Scheduled maintenance tracking and alerts
- **Campaign Management**: Targeted marketing campaigns with analytics
- **Comprehensive Reports**: Revenue, expenses, profit/loss, and customer analytics

## 🚀 Technology Stack

### Backend
- **Framework**: Laravel 10.x
- **PHP**: 8.1+
- **Database**: MySQL 8.0 / PostgreSQL 13+
- **Cache & Queue**: Redis 6.0+
- **Authentication**: Laravel Sanctum

### Key Packages
- `laravel/sanctum` - API authentication
- `laravel/horizon` - Queue monitoring
- `barryvdh/laravel-dompdf` - PDF generation
- `maatwebsite/excel` - Excel export/import
- `spatie/laravel-permission` - Role & permission management
- `spatie/laravel-activitylog` - Audit logging
- `stripe/stripe-php` - Payment processing

### External Services
- **Payment**: Stripe, PayPal
- **SMS**: Twilio
- **Push Notifications**: Firebase Cloud Messaging (FCM)
- **Storage**: AWS S3 / DigitalOcean Spaces
- **Email**: SMTP / SendGrid / Amazon SES

## 📋 Requirements

- PHP >= 8.1
- MySQL >= 8.0 or PostgreSQL >= 13
- Redis >= 6.0
- Composer
- Node.js >= 16.x (for asset compilation)
- SSL Certificate (for production)

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/laundrymart-erp.git
cd laundrymart-erp
```

### 2. Install Dependencies
```bash
composer install
npm install && npm run build
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your configuration:
```env
APP_NAME="LaundryMart ERP"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laundrymart
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
```

### 4. Database Setup
```bash
php artisan migrate --seed
```

### 5. Create Admin User
```bash
php artisan tinker
```
```php
User::create([
    'name' => 'Admin User',
    'email' => 'admin@laundrymart.com',
    'password' => bcrypt('secure_password'),
    'role' => 'admin',
    'status' => 'active'
]);
```

### 6. Start Development Server
```bash
# Start Laravel server
php artisan serve

# Start queue worker (in separate terminal)
php artisan queue:work

# Start scheduler (in separate terminal)
php artisan schedule:work
```

## 📚 API Documentation

### Authentication
```http
POST /api/register
POST /api/login
POST /api/logout
GET  /api/user
```

### Customers
```http
GET    /api/customers
POST   /api/customers
GET    /api/customers/{id}
PUT    /api/customers/{id}
DELETE /api/customers/{id}
```

### Orders
```http
GET    /api/orders
POST   /api/orders
GET    /api/orders/{id}
PUT    /api/orders/{id}
PATCH  /api/orders/{id}/status
```

### Invoices & Payments
```http
GET    /api/invoices
POST   /api/payments
GET    /api/invoices/{id}/pdf
POST   /api/invoices/{id}/send
```

For complete API documentation, see [API Documentation](docs/API.md)

## 🗄️ Database Schema

The system uses 17 main tables:
- `users` - System users (admin, manager, employee, customer)
- `customers` - Customer profiles and loyalty data
- `employees` - Employee information and schedules
- `orders` - Order management with status tracking
- `order_items` - Individual items per order
- `invoices` - Invoice generation and tracking
- `payments` - Payment records and history
- `inventory_supplies` - Stock management
- `equipment` - Equipment and maintenance tracking
- `expenses` - Business expense tracking
- `attendance` - Employee attendance records
- `payroll` - Payroll processing
- `loyalty_transactions` - Loyalty points history
- `campaigns` - Marketing campaigns
- `notifications` - User notifications
- `settings` - System configuration
- `audit_logs` - Activity logging

## 🧪 Testing
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run tests with coverage
php artisan test --coverage
```

## 🚢 Deployment

### Production Setup
```bash
# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Run migrations
php artisan migrate --force

# Seed default settings
php artisan db:seed --class=SettingsSeeder
```

### Queue Worker (Supervisor)
```ini
[program:laundrymart-worker]
command=php /var/www/laundrymart/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=4
```

### Cron Job
```bash
* * * * * cd /var/www/laundrymart && php artisan schedule:run >> /dev/null 2>&1
```

## 📊 Features Overview

### Customer Management
- Complete customer profiles with contact information
- Loyalty program with 4 tiers (Bronze, Silver, Gold, Platinum)
- Automatic tier upgrades based on points
- Customer order history and analytics
- Personalized preferences storage

### Order Processing
- Multi-stage order workflow
- Real-time status tracking with history
- Automatic calculations (subtotal, tax, discounts)
- Priority levels (low, medium, high, urgent)
- Special instructions and notes
- Item-level tracking with metadata

### Financial Management
- Automated invoice generation
- Multiple payment methods (cash, card, transfer, digital wallet)
- Payment tracking and reconciliation
- Expense categorization and approval workflow
- Comprehensive financial reports

### HR Management
- Employee profiles and schedules
- Clock in/out attendance tracking
- Automated payroll calculations
- Performance reviews and ratings
- Department-based organization

### Marketing
- Targeted campaign creation
- Multi-channel delivery (email, SMS, push)
- Campaign analytics and tracking
- Customer segmentation
- ROI measurement

## 🔐 Security Features

- Laravel Sanctum API authentication
- Role-based access control (RBAC)
- Password hashing with bcrypt
- CSRF protection
- SQL injection prevention
- XSS protection
- Rate limiting
- Audit logging
- Secure session management

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

Please ensure your code follows PSR-12 coding standards and includes tests.

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Support

For support and questions:
- 📧 Email: support@laundrymart.com
- 📚 Documentation: [docs.laundrymart.com](https://docs.laundrymart.com)
- 🐛 Issues: [GitHub Issues](https://github.com/yourusername/laundrymart-erp/issues)

## 🙏 Acknowledgments

- Laravel Framework Team
- All contributors and supporters
- Open source community

---

<p align="center">Built with ❤️ using Laravel</p>
<p align="center">© 2025 LaundryMart ERP. All rights reserved.</p>