# Admin Dashboard – Hardcoded Values (Revert Reference)

This file documents hardcoded values on the admin dashboard so they can be reverted to dynamic data later.

**File:** `admin/dashboard.php`  
**Date applied:** 2025-03-10

---

## 1. Vyeti Vilivyopakuliwa (Certificates Downloaded)

**Current (hardcoded):**
```php
<h3 class="mb-1">6,650</h3>
<p class="mb-0">Vyeti Vilivyopakuliwa</p>
```

**To revert (dynamic):**
```php
<h3 class="mb-1"><?= number_format($downloadModel->getTotalDownloadHistory()) ?></h3>
<p class="mb-0">Vyeti Vilivyopakuliwa</p>
```

---

## 2. Watumiaji Wote (All Users)

**Current (hardcoded):**
```php
<h3 class="mb-1">6,319</h3>
<p class="mb-0">Watumiaji Wote</p>
```

**To revert (dynamic):**
```php
<h3 class="mb-1"><?= number_format($totalUsers) ?></h3>
<p class="mb-0">Watumiaji Wote</p>
```

---

## 3. Wanufaika Wote → Success Stories (label only)

**Current:** Card label is "Success Stories" (value remains dynamic).

**To revert (original label):**
In the same card, change:
```php
<p class="mb-0">Success Stories</p>
```
back to:
```php
<p class="mb-0">Wanufaika Wote</p>
```

---

## 4. Wanafunzi (Students)

**Current (hardcoded):**
```php
<h3 class="mb-1">6,300</h3>
<p class="mb-0">Wanafunzi</p>
```

**To revert (dynamic):**
```php
<h3 class="mb-1"><?= number_format($studentUsers) ?></h3>
<p class="mb-0">Wanafunzi</p>
```

---

## Summary

| Card                    | Change type | Hardcoded value | Revert: use |
|-------------------------|------------|------------------|-------------|
| Vyeti Vilivyopakuliwa   | Number     | 6,650            | `$downloadModel->getTotalDownloadHistory()` |
| Watumiaji Wote          | Number     | 6,319            | `$totalUsers` |
| Success Stories         | Label only | "Success Stories"| "Wanufaika Wote" |
| Wanafunzi               | Number     | 6,300            | `$studentUsers` |

The variables `$totalUsers`, `$studentUsers`, and `$downloadModel` are already defined at the top of `admin/dashboard.php`; no other file changes are needed to revert.
