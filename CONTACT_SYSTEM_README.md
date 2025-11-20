# Contact Form System - sparkedev

This document explains how to set up and use the complete contact form system for your sparkedev project.

## 🚀 Features

- **Visitor Contact Form**: Beautiful, responsive contact form on your Next.js frontend
- **Backend Storage**: All submissions stored in Laravel database
- **Email Notifications**: Automatic emails to admins and confirmation emails to users
- **Admin Dashboard**: Complete contact management system in your Laravel admin panel
- **Status Tracking**: Track contact status (new, read, replied, closed)
- **Export Functionality**: Export contacts to CSV
- **Search & Filter**: Advanced search and filtering in admin panel

## 📋 Prerequisites

- Laravel 10+ backend running on `http://localhost:8000`
- Next.js frontend running on `http://localhost:3000`
- MySQL/PostgreSQL database
- SMTP email configuration

## 🛠️ Installation Steps

### 1. Run Database Migration

```bash
cd /path/to/your/laravel/project
php artisan migrate
```

This will create the `contacts` table with the following structure:
- `id` - Primary key
- `name` - Visitor's name
- `email` - Visitor's email
- `company` - Company name (optional)
- `subject` - Message subject
- `message` - Message content
- `project_type` - Type of project (web-development, mobile-app, ui-ux-design, consulting, other)
- `status` - Contact status (new, read, replied, closed)
- `admin_notes` - Internal notes for admins
- `replied_at` - Timestamp when replied
- `created_at` - Submission timestamp
- `updated_at` - Last update timestamp

### 2. Configure Email Settings

Add these lines to your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@sparkedev.com"
MAIL_FROM_NAME="sparkedev"
MAIL_ADMIN_EMAIL="admin@sparkedev.com"
```

### 3. Test Email Configuration

```bash
php artisan tinker
Mail::raw('Test email', function($message) { $message->to('your-email@example.com')->subject('Test'); });
```

## 🔧 Configuration

### Frontend API Configuration

The Next.js frontend is already configured to use:
- Base URL: `http://localhost:8000`
- Contact endpoint: `/api/contact`

### CORS Configuration

Ensure your Laravel backend allows requests from your Next.js frontend:

```php
// config/cors.php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

## 📱 Usage

### For Visitors

1. Navigate to `/contact` page on your Next.js frontend
2. Fill out the contact form with:
   - Name (required)
   - Email (required)
   - Company (optional)
   - Subject (required)
   - Message (required)
   - Project Type (required)
3. Submit the form
4. Receive confirmation email

### For Admins

1. Login to your Laravel admin panel
2. Navigate to `/admin/contacts`
3. View all contact submissions with:
   - Status indicators
   - Search functionality
   - Filter by status
   - Export to CSV
4. Click on any contact to:
   - View full details
   - Update status
   - Add admin notes
   - Reply via email
   - Delete contact

## 📧 Email Templates

### Admin Notification Email

- **Template**: `resources/views/emails/contact/admin-notification.blade.php`
- **Triggered**: When a new contact form is submitted
- **Recipient**: Admin email (configured in `.env`)
- **Content**: Contact details with link to admin dashboard

### User Confirmation Email

- **Template**: `resources/views/emails/contact/user-confirmation.blade.php`
- **Triggered**: When a contact form is submitted
- **Recipient**: Visitor's email
- **Content**: Thank you message with submission summary

## 🔌 API Endpoints

### Public Endpoints

- `POST /api/contact` - Submit contact form

### Protected Endpoints (Require Authentication)

- `GET /api/admin/contacts` - List all contacts
- `GET /api/admin/contacts/{id}` - Get specific contact
- `PUT /api/admin/contacts/{id}` - Update contact
- `DELETE /api/admin/contacts/{id}` - Delete contact
- `GET /api/admin/contacts/statistics` - Get contact statistics

## 🎨 Customization

### Styling

- **Frontend**: Modify `app/pages/Contact.jsx` for form styling
- **Email Templates**: Edit files in `resources/views/emails/contact/`
- **Admin Views**: Customize files in `resources/views/admin/contacts/`

### Fields

To add new fields:
1. Update the migration file
2. Modify the Contact model
3. Update the ContactController
4. Modify the frontend form
5. Update email templates

### Project Types

Current project types:
- `web-development`
- `mobile-app`
- `ui-ux-design`
- `consulting`
- `other`

Add new types in:
- Migration file
- ContactController validation
- Frontend form options

## 🚨 Troubleshooting

### Common Issues

1. **Emails not sending**
   - Check SMTP configuration in `.env`
   - Verify email credentials
   - Check Laravel logs

2. **CORS errors**
   - Ensure CORS is properly configured
   - Check allowed origins

3. **Form submission fails**
   - Verify API endpoint is accessible
   - Check browser console for errors
   - Verify Laravel backend is running

4. **Admin panel not accessible**
   - Ensure admin middleware is working
   - Check user roles and permissions

### Debug Mode

Enable debug mode in Laravel:
```env
APP_DEBUG=true
```

Check logs in `storage/logs/laravel.log`

## 📊 Monitoring

### Dashboard Statistics

The admin dashboard shows:
- Total contacts
- New messages
- Read messages
- Replied messages
- Closed messages
- Today's submissions
- This week's submissions
- This month's submissions

### Export Data

Export contacts to CSV with:
- All contact information
- Filtered by status
- Sorted by date

## 🔒 Security

- Form validation on both frontend and backend
- CSRF protection
- Rate limiting (can be added)
- Admin authentication required for management
- Input sanitization

## 🚀 Deployment

### Production Checklist

- [ ] Update `.env` with production values
- [ ] Set `APP_ENV=production`
- [ ] Configure production SMTP
- [ ] Update CORS origins
- [ ] Set secure session configuration
- [ ] Configure proper database credentials

### Environment Variables

```env
# Production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_HOST=your-production-db-host
DB_DATABASE=your-production-db
DB_USERNAME=your-production-user
DB_PASSWORD=your-production-password

# Email
MAIL_HOST=your-production-smtp
MAIL_USERNAME=your-production-email
MAIL_PASSWORD=your-production-password
```

## 📞 Support

If you encounter any issues:
1. Check this README
2. Review Laravel logs
3. Check browser console
4. Verify database connection
5. Test email configuration

## 🎯 Future Enhancements

Potential improvements:
- Contact form analytics
- Automated responses
- Integration with CRM systems
- File uploads
- Contact preferences
- Newsletter signup
- Appointment scheduling

---

**Happy coding! 🎉**
