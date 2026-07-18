# Apex Office Supply

## Setup
1. Create a MySQL database named `office_equipment_store`.
2. Import `sql/install.sql`
3. Import `sql/images.sql`
4. Update `includes/config.php` with your database username and password.
5. Upload the project to your hosting account.
6. Make sure your host supports PHP sessions and the `mail()` function for the registration confirmation email.

## Default admin account
- Email: `admin@example.com`
- Password: `admin123`

## Main pages
- Buyer: `register.php`, `login.php`, `store.php`, `cart.php`, `checkout.php`, `payment.php`, `about.php`
- Seller: `seller/index.php`, `seller/users.php`, `seller/products.php`, `seller/reports.php`
