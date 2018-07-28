# woocommerce-rent-payment
Wordpress plugin to extend WooCommerce with 'Rent Payment' payment method 

## Installation
1. Upload the folder to your wp-content/plugins directory
2. In your WP-Admin/plugings activate 'WooCommerce Rent Payment'
3. Configure the plugin settings in WP-Admin/WooCommerce/Settings/Payments/Rent Payment

## Troubleshooting
In plugin settings enable API logging. This will record the raw XML requests and responses and will to allow troubleshooting API connectivity problems. 

Don't use this in a production environment as it will store CC information locally.

## Version history

### 0.6

- fix to prevent crash after failed WC upgrade

### 0.5
- i18n
- order status hook
- admin payment

### 0.4 
- error management and logging improved

### 0.3 
- bugfixes

### 0.2 
- basic functionality ready

### 0.1 
- minor fixes

### 0.0 
- initial version
