# Email Configuration Guide

## SMTP Authentication Error Fix

The error you're seeing (`535 Incorrect authentication data`) indicates an SMTP authentication problem. Here's how to fix it:

## Required .env Settings

Based on your GoDaddy hosting (p3plmcpnl497335.prod.phx3.secureserver.net), use these settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.secureserver.net
MAIL_PORT=587
MAIL_USERNAME=your-email@fitscout.com
MAIL_PASSWORD=your-actual-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@fitscout.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Important Notes:

### 1. **Port Configuration**
- **Port 587** with **TLS** encryption (recommended for most servers)
- **Port 465** with **SSL** encryption (alternative, use `MAIL_ENCRYPTION=ssl`)

### 2. **Username vs From Address**
- `MAIL_USERNAME` must be the **actual email account** that exists on your server
- `MAIL_FROM_ADDRESS` can be any email (like no-reply@fitscout.com)
- If you want to send FROM `no-reply@fitscout.com`, that account must exist and be configured in your hosting

### 3. **Common Issues:**

#### Issue 1: Wrong Email Account
If `no-reply@fitscout.com` doesn't exist as an email account:
- Create it in your hosting panel, OR
- Use an existing email account as `MAIL_USERNAME`

#### Issue 2: Wrong Password
- Make sure the password is correct for the email account
- Some hosting requires app-specific passwords
- Check if there are any special characters that need escaping

#### Issue 3: Port/Encryption Mismatch
- Port 587 = TLS encryption
- Port 465 = SSL encryption
- Make sure they match

## Testing Configuration

### Option 1: Test with Mailtrap (Development)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=test@example.com
MAIL_FROM_NAME="FitScout Test"
```

### Option 2: Test with Log Driver (No Email Sent)
```env
MAIL_MAILER=log
```
This will write emails to `storage/logs/laravel.log` instead of sending them.

### Option 3: Use Your Actual GoDaddy Email
1. Log into your GoDaddy hosting panel
2. Create or verify the email account exists: `no-reply@fitscout.com`
3. Get the correct password for that account
4. Use GoDaddy's SMTP settings:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.secureserver.net
   MAIL_PORT=587
   MAIL_USERNAME=no-reply@fitscout.com
   MAIL_PASSWORD=actual_password_here
   MAIL_ENCRYPTION=tls
   ```

## Quick Test Command

After updating `.env`, test with:

```bash
php artisan tinker
```

Then run:
```php
Mail::raw('Test email', function($message) {
    $message->to('your-test@email.com')->subject('Test Email');
});
```

## After Fixing

1. Clear config cache:
   ```bash
   php artisan config:clear
   ```

2. Test registration again - the email verification should work.

3. Remove any test code from `HomeController.php`

## Alternative: Use Mailtrap for Testing

If you want to test without setting up production email:

1. Sign up at https://mailtrap.io
2. Get your SMTP credentials
3. Update `.env` with Mailtrap credentials
4. All emails will be captured in Mailtrap's inbox (not actually sent)

