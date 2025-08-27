# Profile Completion System - Panda Digital V3

## Overview

The Profile Completion System is designed to provide a smooth user experience where users can login with minimal information but must complete their profile when they try to perform specific actions. This system ensures data quality while maintaining user engagement.

## 🎯 Features

### **Step-by-Step Profile Completion**
- Users can login with basic credentials (email + password)
- Profile completion is required only when needed
- Different actions require different levels of profile completion
- Progress tracking with visual indicators

### **Action-Based Requirements**
- **Study Course**: Basic info (first name, last name, phone)
- **Buy Product**: Basic info + region
- **Sell Product**: Basic info + region + business
- **Download Certificate**: Basic info + region
- **Contact Expert**: Basic info + region

### **User Experience**
- Modal-based profile completion
- Dedicated profile completion page
- Progress bars and visual feedback
- Swahili language support
- Mobile-responsive design

## 🚀 Quick Start

### 1. Include the Profile Check Middleware

```php
<?php
require_once __DIR__ . '/includes/profile-check.php';

// Check profile completion for an action
if (!checkProfileCompletion('buy_product', 'Kununua Bidhaa')) {
    return false; // Action cannot proceed
}

// Continue with the action...
?>
```

### 2. Use in JavaScript Functions

```javascript
function purchaseProduct() {
    // Check profile completion
    if (!checkProfileCompletion('buy_product', 'Kununua Bidhaa')) {
        return false;
    }
    
    // Continue with purchase...
}
```

### 3. Display Profile Progress

```php
<?php
// Show profile completion progress
displayProfileProgress('lg'); // Options: 'sm', 'md', 'lg'

// Show requirements for specific action
displayActionRequirements('buy_product');
?>
```

## 📁 File Structure

```
pandadigitalV3/
├── models/
│   └── User.php                          # User model with profile validation
├── includes/
│   ├── profile-check.php                 # Profile check middleware
│   └── profile-completion-modal.php      # Modal component
├── update-profile.php                     # Profile update handler
├── complete-profile.php                   # Dedicated profile page
└── profile-demo.php                      # Demo page
```

## 🔧 Implementation Examples

### Example 1: Market Purchase Button

```php
<!-- In your market page -->
<?php require_once '../includes/profile-check.php'; ?>

<button onclick="purchaseProduct()">Nunua Bidhaa</button>

<script>
function purchaseProduct() {
    // Check profile completion
    if (!checkProfileCompletion('buy_product', 'Kununua Bidhaa')) {
        return false;
    }
    
    // Show purchase modal
    showPurchaseModal();
}
</script>
```

### Example 2: Course Enrollment

```php
<!-- In your course page -->
<?php require_once '../includes/profile-check.php'; ?>

<button onclick="enrollCourse()">Jisajili Kozi</button>

<script>
function enrollCourse() {
    // Check profile completion
    if (!checkProfileCompletion('study_course', 'Kusoma Kozi')) {
        return false;
    }
    
    // Continue with enrollment
    processEnrollment();
}
</script>
```

### Example 3: Expert Contact

```php
<!-- In your expert page -->
<?php require_once '../includes/profile-check.php'; ?>

<button onclick="contactExpert()">Wasiliana na Mtaalamu</button>

<script>
function contactExpert() {
    // Check profile completion
    if (!checkProfileCompletion('contact_expert', 'Kuwasiliana na Mtaalamu')) {
        return false;
    }
    
    // Show contact form
    showContactForm();
}
</script>
```

## 🎨 Customization

### Custom Action Requirements

```php
// In User.php model, modify the actionRequirements array
$actionRequirements = [
    'custom_action' => ['first_name', 'last_name', 'phone', 'custom_field'],
    // Add your custom actions here
];
```

### Custom Field Labels

```php
// In User.php model, modify the getFieldLabels method
public function getFieldLabels() {
    return [
        'first_name' => 'Jina la Kwanza',
        'custom_field' => 'Sehemu Yako',
        // Add your custom fields here
    ];
}
```

### Custom Validation Rules

```php
// In update-profile.php, add custom validation
if (isset($updateData['custom_field'])) {
    // Add your custom validation logic here
    if (!validateCustomField($updateData['custom_field'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Custom field validation failed'
        ]);
        exit;
    }
}
```

## 🔒 Security Features

### Input Validation
- Required field validation
- Phone number format validation (Tanzania)
- Date of birth validation (minimum age 13)
- XSS protection with `htmlspecialchars()`

### Session Security
- Secure session configuration
- User authentication checks
- CSRF protection ready

### Database Security
- PDO prepared statements
- Input sanitization
- SQL injection protection

## 📱 User Interface

### Profile Completion Modal
- Bootstrap 5 responsive design
- Progress bar with percentage
- Form validation
- Success/error messages

### Profile Completion Page
- Dedicated full-page experience
- Visual progress indicators
- Action-specific requirements
- Mobile-optimized layout

## 🧪 Testing

### Demo Page
Visit `/profile-demo.php` to test all profile completion scenarios:

1. **Download Certificate** - Requires full profile
2. **Contact Expert** - Requires full profile
3. **Sell Product** - Requires full profile + business
4. **Buy Product** - Requires basic profile + region
5. **Study Course** - Requires basic profile

### Testing Steps
1. Login with a user account
2. Try different actions
3. Observe profile completion requirements
4. Complete profile and retry actions
5. Verify successful completion

## 🚨 Error Handling

### Common Issues

#### Profile Not Found
```php
// Check if user exists before profile operations
$user = $userModel->getUserById($userId);
if (!$user) {
    // Handle user not found
    header('Location: /login.php');
    exit;
}
```

#### Database Connection Issues
```php
// Ensure database connection is available
if (!isset($pdo)) {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $pdo = $database->getConnection();
}
```

#### Session Issues
```php
// Check session status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verify user is logged in
if (!isset($_SESSION['userId'])) {
    header('Location: /login.php');
    exit;
}
```

## 📊 Database Schema

### Required Tables
```sql
-- Users table (existing)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(255),
    region VARCHAR(255),
    business VARCHAR(255),
    gender VARCHAR(50),
    date_of_birth DATE,
    bio TEXT,
    -- other fields...
);
```

### Profile Completion Fields
- `first_name` - Required for all actions
- `last_name` - Required for all actions
- `phone` - Required for all actions
- `region` - Required for market actions
- `business` - Required for selling products
- `gender` - Optional
- `date_of_birth` - Optional
- `bio` - Optional

## 🔄 Integration with Existing Code

### 1. Add to Header
```php
<!-- In your header.php or main layout -->
<?php require_once __DIR__ . '/includes/profile-check.php'; ?>
```

### 2. Add to Action Buttons
```php
<!-- Replace existing action buttons -->
<button onclick="checkProfileCompletion('action_name', 'Action Display Name')">
    Action Button
</button>
```

### 3. Add to Forms
```php
<!-- Add profile completion check before form submission -->
<form onsubmit="return checkProfileCompletion('action_name', 'Action Display Name')">
    <!-- form fields -->
</form>
```

## 📈 Performance Considerations

### Lazy Loading
- Profile completion modal loads only when needed
- Database queries are optimized with prepared statements
- Session data is cached locally

### Caching
- Profile status is cached in session
- Field labels and options are cached
- Database connections are reused

### Optimization Tips
1. Use `checkProfileCompletion()` for immediate checks
2. Use `requireProfileCompletion()` for redirects
3. Cache profile status in session
4. Minimize database queries

## 🆘 Support

### Common Questions

**Q: How do I add a new action requirement?**
A: Modify the `actionRequirements` array in the `User.php` model.

**Q: Can I customize the required fields?**
A: Yes, modify the `getMissingFieldsForAction()` method in the `User.php` model.

**Q: How do I change the modal appearance?**
A: Modify the CSS in `profile-completion-modal.php` or override with your own styles.

**Q: Can I use this with existing authentication?**
A: Yes, the system works with any existing authentication system that sets `$_SESSION['userId']`.

### Troubleshooting

1. **Modal not showing**: Check if Bootstrap JS is loaded
2. **Profile not updating**: Verify database connection and permissions
3. **Session issues**: Check session configuration in `config/init.php`
4. **Validation errors**: Check browser console for JavaScript errors

## 🎉 Conclusion

The Profile Completion System provides a seamless way to collect user information while maintaining a positive user experience. Users can explore your platform freely and only need to complete their profile when they want to perform specific actions.

This system is:
- ✅ **User-friendly** - No forced upfront data collection
- ✅ **Flexible** - Different actions require different levels of completion
- ✅ **Secure** - Input validation and security measures
- ✅ **Responsive** - Works on all devices
- ✅ **Localized** - Swahili language support
- ✅ **Extensible** - Easy to add new actions and requirements

For more information or support, refer to the demo page at `/profile-demo.php` or check the source code comments.
