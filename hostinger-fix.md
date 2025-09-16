# Hostinger mPDF Fix Instructions

## Problem
`Class "Mpdf\Mpdf" not found` on Hostinger shared hosting

## Solution 1: Force Install mPDF on Hostinger

1. **SSH to your Hostinger account**
2. **Navigate to your project:**
   ```bash
   cd public_html/your-project-name
   ```

3. **Check if vendor directory exists:**
   ```bash
   ls -la vendor/
   ```

4. **Force install mPDF:**
   ```bash
   composer require mpdf/mpdf:^8.2 --no-interaction --ignore-platform-reqs
   composer dump-autoload --optimize
   ```

5. **Check if mPDF is installed:**
   ```bash
   ls -la vendor/mpdf/
   cat composer.json | grep mpdf
   ```

6. **Clear Laravel caches:**
   ```bash
   php artisan optimize:clear
   php artisan config:cache
   ```

## Solution 2: Alternative Route (if mPDF still fails)

Add this alternative route to your `routes/web.php`:

```php
Route::get('/invoices/{seller}/unpaid-html', [App\Http\Controllers\Admin\InvoiceController::class, 'downloadUnpaidHtml'])->name('invoices.unpaid-html');
```

Then add this method to your InvoiceController:

```php
public function downloadUnpaidHtml(User $seller)
{
    $orders = Order::with('seller')
        ->where('seller_id', $seller->id)
        ->where('status', 'livré')
        ->where(function($q) {
            $q->where('facturation_status', 'non payé')
              ->orWhereNull('facturation_status');
        })
        ->get();

    return view('admin.pdf.unpaid_invoices', [
        'seller' => $seller,
        'orders' => $orders,
        'totals' => [
            'count' => $orders->count(),
            'revenue' => $orders->sum('prix_commande'),
            'marge_benefice' => $orders->sum('marge_benefice'),
        ],
        'generatedAt' => now(),
    ])->header('Content-Disposition', 'attachment; filename="unpaid_invoices.html"');
}
```

## Solution 3: Check Hostinger PHP Extensions

Run this to check if required extensions are available:
```bash
php -m | grep -E "(gd|mbstring|zip)"
```

## Debugging Commands

```bash
# Check PHP version
php -v

# Check if composer is working
composer --version

# Check autoloader
php -r "require 'vendor/autoload.php'; var_dump(class_exists('Mpdf\Mpdf'));"

# List installed packages
composer show | grep mpdf
```
