# Add Stock Tracking for SME Products (Module 31)

This plan details the steps required to implement stock tracking for specific products (those belonging to `module_id = 31`). The stock will decrement upon order creation and increment upon order cancellation. An error will be thrown during checkout if a product is out of stock.

## Open Questions
- Should we decrement stock for failed online payments, or only when payment is successful/cash? Currently, the plan decrements stock *immediately* upon checkout to reserve the items. If an online payment fails, the order remains in the system and you might need an automated job to cancel unpaid orders to release the stock, or we can restore stock immediately if the transaction is marked as failed.
- The error message when a product is out of stock will include the product name, as requested.

## Proposed Changes

### Database Migration
#### [NEW] `database/migrations/xxxx_xx_xx_xxxxxx_add_stock_to_products_table.php`
- Create a migration to add a `stock` integer column to the `products` table.
- Make the column `nullable()`.
- Place it after the `quantity_ar` column.

---

### App\Models
#### [MODIFY] `app/Models/Product.php`
- Add `'stock'` to the `$fillable` array.
- Add `'stock' => 'integer'` to the `$casts` array.

#### [MODIFY] `app/Models/Order.php`
- Add a helper method `restoreStock()` to loop through `$this->items`, check if the related product's `module_id == 31`, and increment its stock by the item's quantity. This avoids duplicating the restoration logic across User, Admin, and Vendor controllers.

---

### App\Http\Controllers\Api\User
#### [MODIFY] `app/Http/Controllers/Api/User/OrderController.php`
- **`validateCartItems` method:**
  - Add logic to check if `$product->module_id == 31`.
  - If so, verify that `$product->stock !== null` and `$product->stock >= $item->quantity`.
  - If stock is insufficient, append an error to the `$errors` array containing the product's name (e.g., `"Product '{$product->name}' is out of stock"`).
- **`checkout` method:**
  - Inside the loop where `OrderItem`s are created (`foreach ($cart->items as $cartItem)`), check if `$product->module_id == 31` and `$product->stock !== null`.
  - If true, decrement the stock: `$product->decrement('stock', $cartItem->quantity)`.
- **`cancel` method:**
  - After successfully changing the order status to `cancelled`, call the `$order->restoreStock()` helper method to restore the quantities.

---

### App\Http\Controllers\Api\Admin
#### [MODIFY] `app/Http/Controllers/Api/Admin/OrderController.php`
- **`store` method (Admin Order Creation):**
  - Before creating the order, add a validation step for module 31 products to check if stock is sufficient, returning a 422 error with the product name if it's out of stock.
  - Inside the loop where `OrderItem`s are created, decrement the stock for module 31 products.
- **`cancel` method:**
  - Call the `$order->restoreStock()` helper method after setting the status to `cancelled`.

---

### App\Http\Controllers\Api\Vendor
#### [MODIFY] `app/Http/Controllers/Api/Vendor/OrderController.php`
- **`cancel` method:**
  - Call the `$order->restoreStock()` helper method after setting the status to `cancelled`.

## Verification Plan

### Manual Verification
1. **Database:** Verify that the `stock` column is successfully added to the `products` table.
2. **Product Setup:** Manually set a product in module 31 to have a stock of `2`.
3. **Checkout Validation:**
   - Add `3` units of the product to the cart and attempt checkout. Verify that a 422 error is returned with the product name indicating it is out of stock.
   - Adjust the cart quantity to `2` units and attempt checkout again. Verify that the order succeeds.
4. **Stock Deduction:** Verify the product stock in the database is now `0`.
5. **Order Cancellation:** Cancel the created order (as a User, Vendor, or Admin) and verify the product stock is restored back to `2`.
6. **Non-Module 31 Products:** Verify that products NOT in module 31 do not have their stock tracked or decremented.
