# Email Notification Configuration Guide

## Overview
The Stock Intelligence System includes automated email notifications for stock alerts. When stock levels reach critical thresholds (minimum, overstock, rupture risk, or expiration), administrators and managers receive immediate email notifications.

## System Architecture

### Components
1. **StockMovementObserver** (`app/Observers/StockMovementObserver.php`)
   - Monitors all stock movements
   - Calculates current inventory levels
   - Creates `StockAlert` records when thresholds are exceeded
   - Dispatches `StockAlertNotification` to eligible users

2. **StockAlertNotification** (`app/Notifications/StockAlertNotification.php`)
   - Implements `ShouldQueue` for async processing
   - Formats alert emails in French
   - Includes product details, stock levels, and action links

3. **User Model** (`app/Models/User.php`)
   - Uses `Notifiable` trait (enables notifications)
   - Uses `HasRoles` trait (Spatie Permissions)

4. **Queue System** (configured in `.env`)
   - Uses database-backed queue for reliability
   - Supports supervisor/workers for production

## Configuration Guide

### Step 1: Environment Setup

#### Development (with Mailtrap or MailHog)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@stock-intelligent.local
MAIL_FROM_NAME="Stock Intelligence System"

# For testing locally with MailHog (localhost:1025)
# MAIL_MAILER=smtp
# MAIL_HOST=localhost
# MAIL_PORT=1025
# MAIL_ENCRYPTION=null
```

#### Testing (with Log Driver)
```env
MAIL_MAILER=log
# In test environment, emails are logged to storage/logs/ for inspection
```

#### Production (with SMTP or SendGrid)
```env
# Option 1: Direct SMTP
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server.com
MAIL_PORT=587
MAIL_USERNAME=your-email@company.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=alerts@your-company.com
MAIL_FROM_NAME="Stock Intelligence Alerts"

# Option 2: SendGrid
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=your-sendgrid-api-key
MAIL_FROM_ADDRESS=alerts@your-company.com
MAIL_FROM_NAME="Stock Intelligence Alerts"

# Option 3: AWS SES
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=eu-west-1
MAIL_FROM_ADDRESS=alerts@your-company.com
```

### Step 2: Queue Configuration

```env
QUEUE_CONNECTION=database
# Options: database (local), redis (production), sqs (scalable)
```

**Database Queue** (Default - recommended for small/medium deployments)
- Uses `jobs` table in database
- Requires `php artisan queue:work` running

**Redis Queue** (High performance)
```env
QUEUE_CONNECTION=redis
REDIS_QUEUE_HOST=127.0.0.1
REDIS_QUEUE_PASSWORD=null
REDIS_QUEUE_PORT=6379
REDIS_QUEUE_DB=1
```

### Step 3: Create Database Table for Queue (if using database)

```bash
php artisan queue:table
php artisan migrate
```

This creates the `jobs` and `failed_jobs` tables needed for database-backed queue.

## Alert Types

The system generates 4 types of alerts:

| Alert Type | Trigger | Recipients |
|-----------|---------|-----------|
| `low_stock` | Stock < stock_min | Admin, Gestionnaire |
| `overstock` | Stock > stock_optimal | Admin, Gestionnaire |
| `risk_of_rupture` | stock_min ≤ Stock < stock_min × 1.2 | Admin, Gestionnaire |
| `expiration` | Movement subtype = 'expiration' | Admin, Gestionnaire |

## Testing

### Unit Tests
```bash
# Run email notification tests
php artisan test tests/Feature/EmailNotificationTest.php

# Test specific notification type
php artisan test tests/Feature/EmailNotificationTest.php --filter test_low_stock_alert_notification_is_sent
```

### Manual Testing with MailHog

1. Start MailHog locally:
```bash
# Download from https://github.com/mailhog/MailHog/releases
mailhog
# Access UI at http://localhost:8025
```

2. Configure `.env` for MailHog:
```env
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_ENCRYPTION=null
```

3. Create a test movement:
```bash
php artisan tinker
$product = App\Models\Product::first();
App\Models\StockMovement::factory()->create([
    'product_id' => $product->id,
    'type' => 'exit',
    'quantity' => $product->current_stock - 5
]);

# Check MailHog UI for received email
```

### Testing with Log Driver
```env
MAIL_MAILER=log
# Emails will be saved to storage/logs/laravel.log
```

## Production Deployment

### Queue Worker Setup (Systemd)

Create `/etc/systemd/system/stock-queue.service`:
```ini
[Unit]
Description=Stock Intelligence Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/stock-intelligent
ExecStart=/usr/bin/php artisan queue:work database --sleep=3 --tries=1
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
sudo systemctl enable stock-queue
sudo systemctl start stock-queue
sudo systemctl status stock-queue
```

### Using Supervisor (Alternative)

Install Supervisor:
```bash
sudo apt-get install supervisor
```

Create `/etc/supervisor/conf.d/stock-queue.conf`:
```ini
[program:stock-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/stock-intelligent/artisan queue:work database --sleep=3 --tries=1
autostart=true
autorestart=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/stock-queue.log
```

Restart Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start stock-queue:*
```

### Monitoring

Check queue status:
```bash
# View pending jobs
php artisan queue:size

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

## Troubleshooting

### Notifications Not Sent
1. **Check User Roles**: Verify users have 'admin' or 'gestionnaire' role
   ```bash
   php artisan tinker
   $user = User::find(1);
   $user->roles; // Should include 'admin' or 'gestionnaire'
   ```

2. **Check Database Queue**: Verify jobs table exists
   ```bash
   php artisan queue:table && php artisan migrate
   ```

3. **Start Queue Worker**: Ensure worker is running
   ```bash
   php artisan queue:work
   # Or in background
   php artisan queue:work --daemon
   ```

4. **Check Mail Configuration**: Test mail sending
   ```bash
   php artisan tinker
   Mail::to('test@example.com')->send(new \App\Mails\TestMail());
   ```

### Email Format Issues
1. Check notification class: `app/Notifications/StockAlertNotification.php`
2. Verify product has required fields: `name`, `barcode`, `stock_min`, `stock_optimal`
3. Test message rendering:
   ```bash
   php artisan tinker
   $alert = StockAlert::first();
   $notification = new \App\Notifications\StockAlertNotification($alert);
   echo $notification->toMail(User::first())->getMarkdown();
   ```

### Queue Job Failures
1. Check failed jobs:
   ```bash
   php artisan queue:monitor
   php artisan queue:failed
   ```

2. View error details:
   ```bash
   php artisan tinker
   DB::table('failed_jobs')->latest()->first();
   ```

3. Retry failed notification jobs:
   ```bash
   php artisan queue:retry all
   ```

## Email Notification Recipients

### Current Recipients
- **Role: admin** - Receives all alerts
- **Role: gestionnaire** - Receives all alerts
- **Role: observateur** - Does NOT receive alerts (read-only role)

### Customizing Recipients

To change alert recipients, modify `StockMovementObserver.php`:

```php
protected function sendAlertNotification(StockAlert $alert)
{
    // Example: Send only to administrators
    $users = User::role('admin')->get();
    
    // Example: Send to specific email addresses
    $users = User::whereIn('email', ['manager@company.com'])->get();
    
    if ($users->count() > 0) {
        Notification::send($users, new StockAlertNotification($alert));
    }
}
```

## Email Template Customization

Edit notification email layout:
- **Notification class**: `app/Notifications/StockAlertNotification.php`
- **Mail template**: Uses Laravel's default mail template

To customize, publish mail templates:
```bash
php artisan vendor:publish --tag=laravel-mail
# Edit resources/views/vendor/mail/html/message.blade.php
```

## Database Considerations

### Queue Table Schema
```sql
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL DEFAULT 0,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  UNIQUE KEY `jobs_queue_reserved_at_index` (`queue`, `reserved_at`),
  INDEX `jobs_available_at_index` (`available_at`)
);

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uuid` varchar(255) NOT NULL UNIQUE,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

## Performance Tuning

### Queue Performance
```bash
# Adjust worker sleep time (lower = more responsive, higher resource usage)
php artisan queue:work --sleep=1  # Check every 1 second

# Adjust job timeout
php artisan queue:work --timeout=300  # 5 minutes per job

# Use multiple workers
php artisan queue:work -n & # Background process
php artisan queue:work -n & # Another worker
```

### Email Sending Optimization
- Consider batch sending during off-peak hours
- Implement notification throttling for the same product
- Use transactional email service (Mailgun, SendGrid) for reliability

## Summary Checklist

- [ ] Configure MAIL_MAILER in `.env`
- [ ] Set MAIL_FROM_ADDRESS and MAIL_FROM_NAME
- [ ] Configure QUEUE_CONNECTION in `.env`
- [ ] Create queue table: `php artisan queue:table && php artisan migrate`
- [ ] Test with: `php artisan test tests/Feature/EmailNotificationTest.php`
- [ ] Start queue worker: `php artisan queue:work`
- [ ] Set up supervisor/systemd for production
- [ ] Monitor queue: `php artisan queue:monitor`
- [ ] Document mail credentials in team documentation
