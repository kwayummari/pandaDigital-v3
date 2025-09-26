# Panda Digital Maintenance System

This system allows you to easily put your website into maintenance mode and activate/deactivate it manually.

## Files Created

1. **`maintenance.php`** - The maintenance page that visitors see
2. **`maintenance-control.php`** - Web interface to control maintenance mode
3. **`config/maintenance-check.php`** - System to check for maintenance mode
4. **`enable-maintenance.php`** - Quick command-line style control
5. **`.maintenance`** - Hidden file that controls maintenance mode (created automatically)

## How to Use

### Method 1: Web Interface (Recommended)

1. **Access the control panel:**
   ```
   http://yourdomain.com/maintenance-control.php
   ```

2. **Activate maintenance mode:**
   - Click "Activate Maintenance Mode" button
   - Add optional custom message
   - Click activate

3. **Deactivate maintenance mode:**
   - Click "Deactivate Maintenance Mode" button

### Method 2: Quick Commands

1. **Activate maintenance mode:**
   ```
   http://yourdomain.com/enable-maintenance.php?action=on
   ```

2. **Deactivate maintenance mode:**
   ```
   http://yourdomain.com/enable-maintenance.php?action=off
   ```

3. **Check status:**
   ```
   http://yourdomain.com/enable-maintenance.php?action=status
   ```

### Method 3: Remote Access

For remote access, use the admin key:
```
http://yourdomain.com/maintenance-control.php?admin_key=panda_maintenance_2024
```

## Adding Maintenance Check to Existing Pages

To make any page respect maintenance mode, add this line at the very top (after opening `<?php`):

```php
<?php
require_once 'config/maintenance-check.php';
// ... rest of your page code
```

### Example for index.php:
```php
<?php
require_once 'config/maintenance-check.php';
require_once 'config/init.php';
// ... rest of your code
```

## Security Features

- **IP Whitelist**: Only localhost (127.0.0.1) can access control panels by default
- **Admin Key**: Use `?admin_key=panda_maintenance_2024` for remote access
- **Hidden File**: Maintenance status is stored in `.maintenance` file (hidden from web)

## Customization

### Change Admin Key
Edit the `$adminKey` variable in:
- `maintenance-control.php`
- `config/maintenance-check.php`
- `enable-maintenance.php`

### Customize Maintenance Page
Edit `maintenance.php` to:
- Change colors, fonts, layout
- Update contact information
- Modify progress percentage
- Add your logo

### Add Allowed IPs
Edit the `$allowedIPs` array in:
- `maintenance-control.php`
- `config/maintenance-check.php`
- `enable-maintenance.php`

## How It Works

1. **Activation**: Creates a `.maintenance` file with status data
2. **Detection**: Pages check for this file before loading
3. **Redirect**: If maintenance is active, visitors see maintenance page
4. **Deactivation**: Removes the `.maintenance` file

## Maintenance Page Features

- **Responsive Design**: Works on all devices
- **Auto-refresh**: Refreshes every 30 seconds
- **Progress Bar**: Shows maintenance progress
- **Contact Info**: Displays your contact details
- **Social Links**: Links to your social media
- **Professional Look**: Modern, clean design

## Troubleshooting

### Maintenance mode won't activate
- Check file permissions on the root directory
- Ensure you're accessing from localhost or with admin key

### Maintenance page not showing
- Make sure you've added `require_once 'config/maintenance-check.php';` to your pages
- Check that the `.maintenance` file exists

### Can't deactivate maintenance mode
- Delete the `.maintenance` file manually
- Check file permissions

## Best Practices

1. **Test First**: Always test maintenance mode on a staging site first
2. **Short Duration**: Keep maintenance periods as short as possible
3. **Inform Users**: Use social media to inform users about maintenance
4. **Backup**: Always backup your site before major updates
5. **Monitor**: Keep an eye on the maintenance page during updates

## Emergency Access

If you get locked out:
1. Access your server via FTP/cPanel
2. Delete the `.maintenance` file
3. Your website will be live again

## Support

For issues or questions about the maintenance system, contact:
- Email: info@pandadigital.co.tz
- Phone: +255 767 680 463
- WhatsApp: +255 767 680 463
