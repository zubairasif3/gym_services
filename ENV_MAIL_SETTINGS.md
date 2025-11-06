# Email Configuration for fitscout.it

## Required .env Settings

Based on your mail client manual settings, add/update these in your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.fitscout.it
MAIL_PORT=465
MAIL_USERNAME=no-reply@fitscout.it
MAIL_PASSWORD=your_actual_password_here
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=no-reply@fitscout.it
MAIL_FROM_NAME="FitScout"
```

## Important Notes:

1. **MAIL_PASSWORD** - Must be the actual password for the `no-reply@fitscout.it` email account
2. **MAIL_PORT=465** - Port 465 requires SSL encryption (not TLS)
3. **MAIL_ENCRYPTION=ssl** - Must match port 465

## After Updating .env:

1. Clear config cache:
   ```bash
   php artisan config:clear
   ```

2. Test the registration again - the test mail should send now.

3. Once mail is working, remove the test code from HomeController.php manually.

