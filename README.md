# Kabi Test – Minimal PHP MVC Product Catalogue

Kabi Test is a lightweight PHP project demonstrating a clean custom MVC architecture combined with a modern front‑end pipeline using Vite, SCSS and vanilla ES modules.

It provides:

- A product listing and product detail pages  
- An admin image upload hub with automatic responsive image generation  
- A Swiper-powered product gallery with thumbnails  
- A minimal framework you can extend without heavy dependencies  

---

## Tech Stack

### Backend
- PHP 8+
- Composer (PSR‑4 autoloading)
- Intervention Image (image resizing & WebP generation)

### Frontend
- Vanilla JavaScript ES modules
- SCSS (Dart Sass)
- Swiper.js (product gallery)
- Vite build system

---

## Autoloading (PSR‑4)

This project uses Composer PSR‑4 autoloading.

`composer.json`:

```json
{
  "autoload": {
    "psr-4": {
      "App\\": "src/"
    }
  }
}
```

Examples:

| Namespace | File |
|----------|------|
| App\Core\Router | src/Core/Router.php |
| App\Controllers\ProductController | src/Controllers/ProductController.php |
| App\Services\ImageService | src/Services/ImageService.php |

Whenever you add, rename, or move PHP classes:

```bash
composer dump-autoload
```

This is required for PHP to find new classes.

---

## Installation (Bulletproof Flow)

### 1. Clone project

```bash
git clone https://github.com/kremsnita420/kabi-test.git kabi-test
cd kabi-test/kabi-test
```

### 2. Install PHP dependencies

```bash
composer install
composer dump-autoload
```

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Build assets

Production:

```bash
npm run build
```

Development (hot reload):

```bash
touch .vite-dev
npm run dev
```

### 5. Run locally (quick dev)

```bash
php -S localhost:8000 -t public
```

Open:

```
http://localhost:8000
```

---

## Product Gallery (Swiper Integration)

The product image gallery uses Swiper with synced thumbnails.

### Install

```bash
npm install swiper
```

### JS module

```js
import Swiper from "swiper";
import { Navigation, Thumbs, Keyboard } from "swiper/modules";
import "swiper/css";
```

### Required SCSS (inside grid layouts)

```scss
.product-single__media,
.product-gallery,
.product-gallery__main {
  min-width: 0;
  max-width: 100%;
}

.product-gallery__main {
  overflow: hidden;
}
```

### Features

- Touch swipe
- Keyboard navigation
- Thumbnail syncing
- Stable layout in CSS Grid
- No custom image swapping logic

---

## Development Workflow

### When to run composer dump-autoload

Run whenever you:

- Add a new PHP class
- Rename namespaces
- Move files

```bash
composer dump-autoload
```

### When to rebuild frontend assets

Run when you change:

- JS modules
- SCSS styles
- Vite config

```bash
npm run build
```

### When NOT needed

| Change | Rebuild assets | dump-autoload |
|-------|--------------|---------------|
| Edit PHP logic | ❌ | ❌ |
| Add PHP class | ❌ | ✅ |
| Edit SCSS | ✅ | ❌ |
| Edit JS | ✅ | ❌ |
| Move PHP files | ❌ | ✅ |

---

## Common Composer Issues

### Class not found

Fix:

```bash
composer dump-autoload
```

### Intervention Image not working

Ensure PHP extensions:

```bash
php -m | grep -E 'gd|imagick'
```

Install if missing:

```bash
sudo apt install php-gd
```

(or Imagick if preferred)

### Vendor folder missing

```bash
composer install
```

---

## Apache Production Setup

DocumentRoot must point to:

```
kabi-test/public
```

Enable rewrite:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Virtual host example:

```apache
<VirtualHost *:80>
    ServerName kabi-test.local
    DocumentRoot /var/www/kabi-test/public

    <Directory /var/www/kabi-test/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Routing:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

---

## Production Deployment Checklist

### Backend

- [ ] PHP 8+ installed
- [ ] composer install
- [ ] composer dump-autoload
- [ ] GD or Imagick installed
- [ ] public/assets writable

### Frontend

- [ ] npm install
- [ ] npm run build
- [ ] remove .vite-dev file

### Server

- [ ] Apache/Nginx points to /public
- [ ] rewrite enabled
- [ ] upload directory permissions correct

### Performance

- [ ] WebP images generated
- [ ] assets built (no dev server)
- [ ] PHP opcache enabled (recommended)

---

## Extension Ideas

- Database-backed products
- Authentication for admin upload
- REST API
- Image CDN integration
- Caching layer
- Unit tests

---
