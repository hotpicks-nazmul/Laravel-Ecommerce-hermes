# Multi-Purpose E-Commerce Website Blueprint

## Project Overview

A comprehensive, multi-purpose e-commerce platform built with Laravel that supports various product categories (Food, Technology, Education, Virtual Products) with dynamic theme switching capability from the backend.

---

## 1. Project Structure

```
ecommerce-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── OrderController.php
│   │   │   │   ├── CustomerController.php
│   │   │   │   ├── ThemeController.php
│   │   │   │   ├── SettingController.php
│   │   │   │   ├── PaymentGatewayController.php
│   │   │   │   ├── SEOController.php
│   │   │   │   └── ChatController.php
│   │   │   ├── Frontend/
│   │   │   │   ├── HomeController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── CartController.php
│   │   │   │   ├── CheckoutController.php
│   │   │   │   ├── WishlistController.php
│   │   │   │   ├── SearchController.php
│   │   │   │   └── PageController.php
│   │   │   ├── API/
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── CartController.php
│   │   │   │   ├── ChatController.php
│   │   │   │   └── PaymentController.php
│   │   │   └── Auth/
│   │   │       ├── LoginController.php
│   │   │       ├── RegisterController.php
│   │   │       └── SocialAuthController.php
│   │   ├── Middleware/
│   │   │   ├── ThemeMiddleware.php
│   │   │   ├── SecurityMiddleware.php
│   │   │   ├── SEOMiddleware.php
│   │   │   └── InstallationMiddleware.php
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Cart.php
│   │   ├── CartItem.php
│   │   ├── Wishlist.php
│   │   ├── Theme.php
│   │   ├── Setting.php
│   │   ├── PaymentGateway.php
│   │   ├── SEOSetting.php
│   │   ├── Page.php
│   │   ├── Blog.php
│   │   ├── Coupon.php
│   │   ├── Review.php
│   │   ├── Chat.php
│   │   ├── ChatMessage.php
│   │   └── Installation.php
│   ├── Services/
│   │   ├── ThemeService.php
│   │   ├── PaymentService.php
│   │   ├── SEOService.php
│   │   ├── ChatService.php
│   │   └── InstallationService.php
│   └── Helpers/
│       └── Helper.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/
│   │   ├── themes/
│   │   │   ├── food/
│   │   │   │   ├── layouts/
│   │   │   │   ├── partials/
│   │   │   │   ├── home/
│   │   │   │   ├── products/
│   │   │   │   ├── cart/
│   │   │   │   └── checkout/
│   │   │   ├── technology/
│   │   │   │   ├── layouts/
│   │   │   │   ├── partials/
│   │   │   │   └── ...
│   │   │   ├── education/
│   │   │   │   ├── layouts/
│   │   │   │   ├── partials/
│   │   │   │   └── ...
│   │   │   └── virtual/
│   │   │       ├── layouts/
│   │   │       ├── partials/
│   │   │       └── ...
│   │   ├── admin/
│   │   │   ├── layouts/
│   │   │   ├── dashboard/
│   │   │   ├── products/
│   │   │   ├── categories/
│   │   │   ├── orders/
│   │   │   ├── themes/
│   │   │   ├── settings/
│   │   │   └── installation/
│   │   ├── auth/
│   │   ├── emails/
│   │   └── errors/
│   ├── js/
│   │   ├── app.js
│   │   ├── admin.js
│   │   └── components/
│   └── sass/
│       ├── themes/
│       └── admin/
├── public/
│   ├── themes/
│   │   ├── food/
│   │   │   ├── css/
│   │   │   ├── js/
│   │   │   └── images/
│   │   ├── technology/
│   │   ├── education/
│   │   └── virtual/
│   ├── admin/
│   └── uploads/
├── routes/
│   ├── web.php
│   ├── api.php
│   └── admin.php
├── config/
│   ├── themes.php
│   ├── payment.php
│   └── seo.php
└── install/
    ├── InstallerController.php
    └── views/
```

---

## 2. Database Schema

### Core Tables

#### Users Table
```sql
- id (bigint, primary key)
- name (string)
- email (string, unique)
- phone (string, nullable)
- password (string)
- role (enum: admin, customer, vendor)
- avatar (string, nullable)
- email_verified_at (timestamp)
- status (enum: active, inactive, banned)
- remember_token
- created_at, updated_at
```

#### Categories Table
```sql
- id (bigint, primary key)
- parent_id (bigint, nullable, foreign key)
- name (string)
- slug (string, unique)
- description (text, nullable)
- image (string, nullable)
- icon (string, nullable)
- theme_type (enum: food, technology, education, virtual, general)
- meta_title (string, nullable)
- meta_description (text, nullable)
- status (enum: active, inactive)
- sort_order (integer)
- created_at, updated_at
```

#### Products Table
```sql
- id (bigint, primary key)
- category_id (bigint, foreign key)
- name (string)
- slug (string, unique)
- sku (string, unique)
- short_description (text)
- long_description (text)
- price (decimal 10,2)
- sale_price (decimal 10,2, nullable)
- cost_price (decimal 10,2, nullable)
- quantity (integer)
- stock_status (enum: in_stock, out_of_stock, backorder)
- weight (decimal 8,2, nullable)
- dimensions (string, nullable)
- images (json)
- featured_image (string)
- gallery (json, nullable)
- attributes (json, nullable)
- variations (json, nullable)
- tags (json, nullable)
- is_featured (boolean)
- is_digital (boolean)
- download_link (string, nullable)
- meta_title (string, nullable)
- meta_description (text, nullable)
- meta_keywords (string, nullable)
- status (enum: active, inactive, draft)
- created_by (bigint, foreign key)
- created_at, updated_at
```

#### Orders Table
```sql
- id (bigint, primary key)
- order_number (string, unique)
- user_id (bigint, foreign key)
- status (enum: pending, processing, confirmed, shipped, delivered, cancelled, refunded)
- payment_status (enum: pending, paid, failed, refunded)
- payment_method (string)
- payment_gateway (string, nullable)
- transaction_id (string, nullable)
- subtotal (decimal 12,2)
- tax (decimal 10,2)
- shipping_cost (decimal 10,2)
- discount (decimal 10,2)
- coupon_code (string, nullable)
- total (decimal 12,2)
- billing_first_name (string)
- billing_last_name (string)
- billing_email (string)
- billing_phone (string)
- billing_address (text)
- billing_city (string)
- billing_state (string)
- billing_postcode (string)
- billing_country (string)
- shipping_first_name (string)
- shipping_last_name (string)
- shipping_email (string)
- shipping_phone (string)
- shipping_address (text)
- shipping_city (string)
- shipping_state (string)
- shipping_postcode (string)
- shipping_country (string)
- notes (text, nullable)
- created_at, updated_at
```

#### Order Items Table
```sql
- id (bigint, primary key)
- order_id (bigint, foreign key)
- product_id (bigint, foreign key)
- product_name (string)
- variation (json, nullable)
- quantity (integer)
- price (decimal 10,2)
- total (decimal 12,2)
- created_at, updated_at
```

#### Carts Table
```sql
- id (bigint, primary key)
- user_id (bigint, nullable, foreign key)
- session_id (string, nullable)
- created_at, updated_at
```

#### Cart Items Table
```sql
- id (bigint, primary key)
- cart_id (bigint, foreign key)
- product_id (bigint, foreign key)
- quantity (integer)
- variation (json, nullable)
- price (decimal 10,2)
- created_at, updated_at
```

#### Wishlists Table
```sql
- id (bigint, primary key)
- user_id (bigint, foreign key)
- product_id (bigint, foreign key)
- created_at, updated_at
```

### Theme & Settings Tables

#### Themes Table
```sql
- id (bigint, primary key)
- name (string)
- slug (string, unique)
- description (text, nullable)
- category_type (enum: food, technology, education, virtual, general)
- preview_image (string)
- is_active (boolean)
- settings (json, nullable)
- created_at, updated_at
```

#### Settings Table
```sql
- id (bigint, primary key)
- key (string, unique)
- value (text, nullable)
- group (string, nullable)
- type (enum: text, textarea, image, select, checkbox, json)
- created_at, updated_at
```

#### Payment Gateways Table
```sql
- id (bigint, primary key)
- name (string)
- slug (string, unique)
- description (text, nullable)
- logo (string, nullable)
- credentials (json, nullable)
- is_active (boolean)
- test_mode (boolean)
- sort_order (integer)
- created_at, updated_at
```

### SEO Tables

#### SEO Settings Table
```sql
- id (bigint, primary key)
- page_type (string)
- page_id (bigint, nullable)
- meta_title (string)
- meta_description (text)
- meta_keywords (text)
- og_title (string, nullable)
- og_description (text, nullable)
- og_image (string, nullable)
- twitter_title (string, nullable)
- twitter_description (text, nullable)
- twitter_image (string, nullable)
- schema_markup (json, nullable)
- canonical_url (string, nullable)
- robots (string, nullable)
- created_at, updated_at
```

#### Redirects Table
```sql
- id (bigint, primary key)
- old_url (string, unique)
- new_url (string)
- status_code (integer)
- created_at, updated_at
```

### Content Tables

#### Pages Table
```sql
- id (bigint, primary key)
- title (string)
- slug (string, unique)
- content (longtext)
- featured_image (string, nullable)
- meta_title (string, nullable)
- meta_description (text, nullable)
- status (enum: published, draft)
- created_by (bigint, foreign key)
- created_at, updated_at
```

#### Blogs Table
```sql
- id (bigint, primary key)
- title (string)
- slug (string, unique)
- excerpt (text)
- content (longtext)
- featured_image (string, nullable)
- category_id (bigint, foreign key)
- tags (json, nullable)
- author_id (bigint, foreign key)
- meta_title (string, nullable)
- meta_description (text, nullable)
- status (enum: published, draft)
- published_at (timestamp, nullable)
- created_at, updated_at
```

#### Coupons Table
```sql
- id (bigint, primary key)
- code (string, unique)
- type (enum: percentage, fixed)
- value (decimal 10,2)
- min_order_amount (decimal 10,2, nullable)
- max_discount (decimal 10,2, nullable)
- usage_limit (integer, nullable)
- used_count (integer)
- start_date (timestamp)
- end_date (timestamp)
- status (enum: active, inactive)
- created_at, updated_at
```

#### Reviews Table
```sql
- id (bigint, primary key)
- product_id (bigint, foreign key)
- user_id (bigint, foreign key)
- rating (integer)
- title (string)
- comment (text)
- images (json, nullable)
- status (enum: approved, pending, rejected)
- created_at, updated_at
```

### Chat Tables

#### Chats Table
```sql
- id (bigint, primary key)
- user_id (bigint, nullable, foreign key)
- session_id (string, nullable)
- status (enum: open, closed, pending)
- assigned_to (bigint, nullable, foreign key)
- created_at, updated_at
```

#### Chat Messages Table
```sql
- id (bigint, primary key)
- chat_id (bigint, foreign key)
- sender_type (enum: user, admin, bot)
- sender_id (bigint, nullable)
- message (text)
- attachments (json, nullable)
- is_read (boolean)
- created_at, updated_at
```

---

## 3. Theme System Architecture

### Theme Structure
Each theme will have:
- **Layout files** (Blade templates)
- **Assets** (CSS, JS, Images)
- **Configuration** (theme.json)
- **Components** (Reusable UI components)

### Theme Configuration (theme.json)
```json
{
    "name": "Food Theme",
    "slug": "food",
    "version": "1.0.0",
    "category": "food",
    "author": "Your Company",
    "description": "A beautiful theme for food e-commerce",
    "colors": {
        "primary": "#ff6b35",
        "secondary": "#f7c59f",
        "accent": "#2ec4b6"
    },
    "fonts": {
        "heading": "Poppins",
        "body": "Open Sans"
    },
    "features": {
        "hero_slider": true,
        "featured_products": true,
        "category_grid": true,
        "newsletter": true,
        "testimonials": true
    },
    "layouts": {
        "home": "home",
        "product": "product-detail",
        "category": "category",
        "cart": "cart",
        "checkout": "checkout"
    }
}
```

### Theme Middleware
The ThemeMiddleware will:
1. Check active theme from database
2. Load theme configuration
3. Set view paths dynamically
4. Inject theme assets

### Available Themes
1. **Food Theme** - Warm colors, food-focused layouts, recipe sections
2. **Technology Theme** - Modern, sleek design, product comparisons
3. **Education Theme** - Clean, professional, course-focused
4. **Virtual Products Theme** - Minimal, download-focused

---

## 4. Payment Gateways (Bangladesh)

### Supported Gateways
1. **bKash** - Mobile financial service
2. **Nagad** - Mobile financial service
3. **Rocket** - Mobile financial service
4. **SSLCommerz** - Payment gateway aggregator
5. **PortWallet** - Payment gateway
6. **AmarPay** - Payment gateway
7. **Cash on Delivery (COD)**

### Payment Gateway Integration
```php
// config/payment.php
return [
    'gateways' => [
        'bkash' => [
            'name' => 'bKash',
            'class' => \App\Services\Payments\BkashGateway::class,
            'credentials' => [
                'app_key' => env('BKASH_APP_KEY'),
                'app_secret' => env('BKASH_APP_SECRET'),
                'username' => env('BKASH_USERNAME'),
                'password' => env('BKASH_PASSWORD'),
            ],
        ],
        'sslcommerz' => [
            'name' => 'SSLCommerz',
            'class' => \App\Services\Payments\SSLCommerzGateway::class,
            'credentials' => [
                'store_id' => env('SSL_STORE_ID'),
                'store_password' => env('SSL_STORE_PASSWORD'),
            ],
        ],
        // ... other gateways
    ],
];
```

---

## 5. SEO Features

### On-Page SEO
- Dynamic meta tags (title, description, keywords)
- Open Graph tags for social sharing
- Twitter Card support
- Canonical URLs
- Schema.org structured data
- XML Sitemap generation
- Robots.txt management

### Technical SEO
- Clean URL structure
- Breadcrumb navigation
- Image optimization with alt tags
- Page speed optimization
- Mobile-responsive design
- HTTPS enforcement

### SEO Schema Types
- Product schema
- Organization schema
- BreadcrumbList schema
- Review schema
- FAQ schema
- Article schema (for blogs)

---

## 6. Security Features

### Authentication & Authorization
- Secure password hashing (bcrypt)
- Email verification
- Two-factor authentication (optional)
- Role-based access control
- Session management
- Password reset functionality
- Social login (Google, Facebook)

### Security Measures
- CSRF protection
- XSS prevention
- SQL injection prevention (Eloquent ORM)
- Rate limiting
- Input validation & sanitization
- Secure file uploads
- HTTPS enforcement
- Security headers
- Activity logging
- IP whitelisting for admin

### Data Protection
- Sensitive data encryption
- Secure payment processing
- GDPR compliance features
- Privacy policy management

---

## 7. Live Chat & AI Chatbot

### Live Chat Features
- Real-time messaging
- Multiple chat operators
- Chat assignment
- File sharing
- Typing indicators
- Read receipts
- Chat history
- Offline messages

### AI Chatbot Features
- Product recommendations
- Order status inquiry
- FAQ responses
- Natural language processing
- Multi-language support
- Handoff to human agent
- Training interface for admin

### Chat Architecture
```
Frontend (WebSocket Client)
    ↓
WebSocket Server (Laravel Echo + Pusher/Soketi)
    ↓
ChatController → ChatService
    ↓
Database (chats, chat_messages)
```

---

## 8. Installation Wizard

### Installation Steps
1. **Welcome Screen** - Introduction and requirements check
2. **Server Requirements** - PHP version, extensions, permissions
3. **Database Configuration** - Host, database name, username, password
4. **Site Configuration** - Site name, admin credentials, timezone
5. **Theme Selection** - Choose initial theme
6. **Payment Gateway Setup** - Configure payment methods
7. **Installation Complete** - Success message and login redirect

### Installation Controller Flow
```php
class InstallerController extends Controller
{
    public function welcome() { /* Step 1 */ }
    public function requirements() { /* Step 2 */ }
    public function database() { /* Step 3 */ }
    public function setupDatabase() { /* Process DB setup */ }
    public function siteConfig() { /* Step 4 */ }
    public function saveSiteConfig() { /* Process site config */ }
    public function theme() { /* Step 5 */ }
    public function saveTheme() { /* Process theme selection */ }
    public function payment() { /* Step 6 */ }
    public function savePayment() { /* Process payment config */ }
    public function complete() { /* Step 7 */ }
}
```

---

## 9. Core E-Commerce Features

### Product Management
- Product CRUD operations
- Product variations (size, color, etc.)
- Product attributes
- Product categories & tags
- Product images & gallery
- Digital products support
- Product import/export
- Bulk editing

### Inventory Management
- Stock tracking
- Low stock alerts
- Stock status management
- Backorder support

### Order Management
- Order processing workflow
- Order status updates
- Order notes
- Invoice generation
- Packing slips
- Shipping labels

### Customer Management
- Customer profiles
- Address book
- Order history
- Wishlist
- Reviews

### Marketing Features
- Coupons & discounts
- Flash sales
- Featured products
- Related products
- Cross-sells & up-sells
- Newsletter subscription

### Shipping
- Shipping zones
- Shipping methods
- Flat rate shipping
- Free shipping
- Local pickup

---

## 10. Admin Panel Features

### Dashboard
- Sales overview
- Order statistics
- Product statistics
- Customer statistics
- Recent orders
- Low stock alerts
- Revenue charts

### Product Management
- Products list with filters
- Add/Edit products
- Categories management
- Attributes management
- Reviews moderation

### Order Management
- Orders list with filters
- Order details
- Order status updates
- Refund processing

### Customer Management
- Customers list
- Customer details
- Customer orders
- Customer reviews

### Theme Management
- Theme browser
- Theme activation
- Theme customization
- Theme settings

### Settings
- General settings
- Payment gateways
- Shipping settings
- Tax settings
- Email settings
- SEO settings
- Security settings

### Content Management
- Pages management
- Blog management
- Menus management
- Media library

### Reports
- Sales reports
- Product reports
- Customer reports
- Traffic reports

---

## 11. API Endpoints

### Public API
```
GET    /api/products              - List products
GET    /api/products/{slug}       - Product details
GET    /api/categories            - List categories
GET    /api/categories/{slug}     - Category details
POST   /api/cart/add              - Add to cart
GET    /api/cart                  - Get cart
PUT    /api/cart/update           - Update cart
DELETE /api/cart/remove           - Remove from cart
POST   /api/search                - Search products
```

### Authenticated API
```
POST   /api/auth/register         - User registration
POST   /api/auth/login            - User login
POST   /api/auth/logout           - User logout
GET    /api/user                  - User profile
PUT    /api/user                  - Update profile
GET    /api/user/orders           - User orders
GET    /api/user/wishlist         - User wishlist
POST   /api/checkout              - Process checkout
```

### Chat API
```
POST   /api/chat/start            - Start chat
POST   /api/chat/message          - Send message
GET    /api/chat/messages         - Get messages
POST   /api/chat/bot              - AI chatbot query
```

---

## 12. Technology Stack

### Backend
- **Framework**: Laravel 11.x
- **PHP Version**: 8.2+
- **Database**: MySQL 8.0+
- **Cache**: Redis (optional)
- **Queue**: Redis/Database

### Frontend
- **Template Engine**: Blade
- **CSS Framework**: 
  - **Tailwind CSS** for frontend themes (customizable, unique designs per theme)
  - **Bootstrap 5** for admin panel (component-rich, faster development)
- **JavaScript**: Vanilla JS / Alpine.js
- **Build Tool**: Vite

### Real-time
- **WebSocket**: Laravel Echo + Pusher/Soketi
- **Broadcasting**: Laravel Broadcasting

### Third-party Services
- **Email**: SMTP / Mailgun / Sendgrid
- **Storage**: Local / S3
- **Analytics**: Google Analytics

---

## 13. File Structure for Themes

```
resources/views/themes/{theme_name}/
├── layouts/
│   ├── app.blade.php
│   ├── auth.blade.php
│   └── email.blade.php
├── partials/
│   ├── header.blade.php
│   ├── footer.blade.php
│   ├── sidebar.blade.php
│   ├── navbar.blade.php
│   └── breadcrumbs.blade.php
├── components/
│   ├── product-card.blade.php
│   ├── category-card.blade.php
│   ├── cart-item.blade.php
│   └── review.blade.php
├── home/
│   ├── index.blade.php
│   ├── hero.blade.php
│   ├── featured.blade.php
│   └── categories.blade.php
├── products/
│   ├── index.blade.php
│   ├── show.blade.php
│   └── search.blade.php
├── cart/
│   ├── index.blade.php
│   └── mini-cart.blade.php
├── checkout/
│   ├── index.blade.php
│   ├── payment.blade.php
│   └── thank-you.blade.php
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   └── forgot-password.blade.php
├── pages/
│   ├── about.blade.php
│   ├── contact.blade.php
│   └── terms.blade.php
└── errors/
    └── 404.blade.php
```

---

## 14. Development Phases

### Phase 1: Foundation (Week 1-2)
- Laravel project setup
- Database migrations
- Authentication system
- Installation wizard
- Basic admin panel

### Phase 2: Core Features (Week 3-4)
- Product management
- Category management
- Cart functionality
- Order management
- Customer management

### Phase 3: Theme System (Week 5-6)
- Theme architecture
- Theme middleware
- Default theme (Food)
- Theme admin interface
- Theme customization

### Phase 4: Payment & Checkout (Week 7-8)
- Checkout process
- Payment gateway integration
- Order processing
- Invoice generation

### Phase 5: SEO & Security (Week 9-10)
- SEO implementation
- Security hardening
- Performance optimization
- Testing

### Phase 6: Additional Features (Week 11-12)
- Live chat system
- AI chatbot
- Blog system
- Reports & analytics
- Documentation

---

## 15. Configuration Files

### Environment Variables (.env.example)
```env
APP_NAME="E-Commerce"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=

# Payment Gateways
BKASH_APP_KEY=
BKASH_APP_SECRET=
BKASH_USERNAME=
BKASH_PASSWORD=

SSL_STORE_ID=
SSL_STORE_PASSWORD=

# Chat
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https

# Mail
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=

# AI Chatbot
OPENAI_API_KEY=
```

---

## 16. Quality Assurance

### Testing Strategy
- Unit tests for models and services
- Feature tests for controllers
- Browser tests (Laravel Dusk)
- API tests
- Performance testing

### Code Quality
- PSR-12 coding standards
- Laravel Pint for formatting
- PHPStan for static analysis
- Code reviews

---

## 17. Deployment Checklist

- [ ] Server requirements verified
- [ ] Environment variables configured
- [ ] Database migrated
- [ ] Storage linked
- [ ] Cache configured
- [ ] Queue worker running
- [ ] SSL certificate installed
- [ ] Backup system configured
- [ ] Monitoring enabled
- [ ] Error logging configured

---

## 18. Post-Launch Support

### Maintenance Tasks
- Regular security updates
- Database backups
- Performance monitoring
- Error tracking
- Feature updates

### Documentation
- User manual
- Admin guide
- Developer documentation
- API documentation

---

This blueprint provides a comprehensive foundation for building a multi-purpose e-commerce platform with Laravel. The modular architecture allows for easy extension and customization while maintaining code quality and security standards.
