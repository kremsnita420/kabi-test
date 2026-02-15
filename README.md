# Kabi Test – Hardened Minimal MVC Scaffold

Kabi Test is a lightweight PHP project that demonstrates how to build a small product‑catalogue website with a bespoke MVC architecture.  It combines a simple router, controller and view layer with a Vue‑free front end built using vanilla JavaScript modules, SCSS and the [Vite](https://vitejs.dev/) build tool.  The site renders a home page, informational pages (About, Contact, Write to Us), a product listing, detailed product pages and an admin “upload hub” for adding product images.  Uploaded images are automatically converted into multiple responsive variants (hero, thumbnail and `@2x` WebP/JPG versions) using the Intervention Image library.  The codebase is intentionally minimal and easy to extend, making it a good starting point for learning custom PHP architectures or prototyping small e‑commerce sites.

## Features

- **Minimal MVC framework** – A tiny router and base controller deliver clean URL handling and view rendering without a heavy framework.
- **Product catalogue** – `ProductController` lists products and renders detailed product pages.  A helper decorates each product with responsive image variants for the gallery.
- **Admin image upload** – Administrators can upload JPEG/PNG files via `/admin/upload`.  The `UploadController` validates input, stores originals and delegates to `ImageService` to generate hero and thumbnail variants in JPG and WebP formats (plus `@2x` versions for HiDPI devices).  A small JS app (`admin-upload-hub.js`) powers the drag‑and‑drop interface and shows JSON responses.
- **Responsive front end** – Styles are written in modular SCSS with BEM naming.  Navigation toggles adapt automatically on mobile.  The product gallery uses `<picture>` elements and JavaScript to switch images when thumbnails are clicked.
- **Asset pipeline with Vite** – The Node/Vite setup compiles ES module JavaScript and SCSS, injects module aliases, and outputs predictable filenames in `public/build`.  During development a `.vite-dev` marker file triggers hot module reloading and on‑the‑fly SCSS processing.
- **Image variant helper** – The `App\Views\Helpers\Picture` helper outputs `<picture>` tags with appropriate `source`/`srcset` attributes, skipping missing variants to avoid 404s.
- **No database required (demo mode)** – Products are defined in an in‑memory array (`ProductRepository`).  Images live in `public/assets/product-images/<slug>` and are discovered dynamically.  This design makes deployment simple but can be swapped for a real database.

## Architecture Overview

The project follows a conventional MVC pattern with a custom implementation.  At a high level, the runtime flow is:

1. **Entry point** – `public/index.php` is the front controller.  It autoloads dependencies via Composer, instantiates a `Router`, registers routes and handlers, and calls `dispatch()`.
2. **Router** – `App\Core\Router` normalises the request path, matches it against registered route patterns (supports parameterised segments like `/product/{id:\d+}`), extracts parameters and calls the designated controller method.  If no route matches, a not‑found handler renders a 404 page.
3. **Controllers** – Controllers extend `App\Core\Controller` which provides a `render()` helper.  Significant controllers include:
   - `PageController` – Renders static pages (home, about, contact, write‑to‑us).
   - `ProductController` – Lists all products (`index()`) and displays a single product (`show()`).  It retrieves data from `ProductRepository` and passes it to the views.
   - `Admin\UploadHubController` – Renders the admin upload page with a list of available products.  It injects the JS entrypoint for the upload UI via the `$pageScripts` variable.
   - `Admin\UploadController` – Handles POST requests when an image is uploaded.  It validates the slug and file, stores the uploaded file in `/public/assets/product-images/{slug}`, converts PNG to JPG, and uses `ImageService` to generate hero/thumb variant sets.  A JSON payload describing the stored files is returned.
   - `NotFoundController` – Fallback 404 page.
4. **Models** – `ProductRepository` stores product metadata in a PHP array.  Its `decorateImages()` method scans the file system for uploaded images and builds a `gallery_items` array containing hero/thumb variant sets.  This avoids repeated image discovery in views.
5. **Views** – Views live under `src/Views`.  `layout.php` defines the overall HTML skeleton and conditionally loads Vite dev scripts or built assets.  Specific pages are rendered in the `<main>` section.  Partials (header, footer, product list) and helpers (the `Picture` class) encapsulate reusable markup.  The product gallery is implemented using `<picture>` elements and populated dynamically by the JS module.
6. **Services** – `ImageService` wraps the Intervention Image library.  Given an uploaded image, it crops it to fixed dimensions for hero (`1000×700`) and thumbnail (`300×300`) variants, saving JPG and WebP versions at 1× and 2× resolutions.
7. **Front‑end assets** – Client‑side JavaScript modules live in `resources/js`.  They initialise the navigation toggle (`modules/nav.js`), handle product gallery switching (`modules/products.js`) and implement the admin upload page (`pages/admin-upload-hub.js`).  SCSS styles live in `resources/scss` and are organised into `abstracts`, `base`, `components`, `layout`, `pages` and `utilities` folders.  Vite bundles these assets and outputs them into `public/build`.

### Folder Structure

```
.
├── composer.json             # PHP dependencies (only Intervention Image is required)
├── public/                   # Publicly served assets
│   ├── index.php             # Front controller
│   ├── assets/               # Fonts, images and product images
│   └── build/                # Vite‑generated JS/CSS (after running `npm run build`)
├── resources/                # Front‑end source
│   ├── js/                   # ES modules for navigation, products and admin upload
│   └── scss/                 # SCSS organised by responsibility (abstracts, components, layout…)
├── src/                      # PHP application
│   ├── Core/                 # Base Controller and Router classes
│   ├── Controllers/          # Page, product and admin controllers
│   ├── Models/               # ProductRepository
│   ├── Services/             # ImageService (uses Intervention Image)
│   └── Views/                # Layout, pages, partials and view helpers
├── vendor/                   # Composer dependencies (Intervention Image)
└── vite.config.js            # Vite configuration and build pipeline
```

## Tech Stack

- **Backend:** PHP 8+, Composer, custom MVC classes.  The only third‑party PHP dependency is `intervention/image` (for image manipulation).
- **Frontend:** Vanilla ES modules, SCSS compiled with Dart Sass via Vite.  Navigation and gallery behaviour are implemented without frameworks.  FontAwesome provides icons, and Open Sans fonts are bundled locally.
- **Build Tools:** Vite orchestrates JavaScript and SCSS compilation.  A `.vite-dev` file in the project root enables dev mode; otherwise built assets in `public/build` are used.  In dev mode Vite runs a local HMR server on port `5173`.
- **Image Processing:** Intervention Image (GD driver by default) converts uploaded images into multiple sizes and formats.  If Imagick is available, swapping `new Driver()` for `new Imagick\Driver()` in `ImageService` can improve quality and performance.

## PSR-4 autoloading via Composer
`composer.json` defines:

```json
"autoload": { "psr-4": { "App\\": "src/" } }
```
## Installation

1. **Clone the repository** and change into the project directory:

   ```sh
   git clone <repo-url> kabi-test
   cd kabi-test/kabi-test
   ```

2. **Install PHP dependencies**:

   ```sh
   composer install
   composer dump-autoload
   ```

3. **Install Node dependencies** for the asset pipeline:

   ```sh
   npm install
   ```

4. **Build assets** for production:

   ```sh
   npm run build
   ```

   During development you can run the Vite dev server instead of building:

   ```sh
   touch .vite-dev       # tells layout.php to use the dev server
   npm run dev
   ```

5. **Serve the application.**  You can use PHP’s built‑in server for local development:

   ```sh
   php -S localhost:8000 -t public
   ```

   Then visit `http://localhost:8000` in your browser.

## Serving the Project Locally with Apache

This project is designed to run behind a classic Apache + PHP setup using `public/` as the document root.

### Move project into Apache web root

```bash
sudo mv kabi-test /var/www/kabi-test
```

### Enable rewrite

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Virtual host

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

### Routing

Create `public/.htaccess`

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

### Install

```bash
composer install
npm install
npm run build
```

### Permissions

```bash
sudo chmod -R 775 public/assets
```

### Access

http://kabi-test.local  
http://kabi-test.local/admin/upload


## Configuration

- **Dev vs production mode** – The presence of a `.vite-dev` file in the project root controls whether `layout.php` loads Vite’s dev server or the built assets.  Delete this file for production.
- **Image driver** – `ImageService` defaults to the GD driver.  If Imagick is installed, replace `new Driver()` with `new Imagick\Driver()` in `src/Services/ImageService.php` for better memory usage.
- **Upload paths** – Uploaded images are stored under `public/assets/product-images/{slug}`.  Ensure this directory is writable by the web server.  Variant files are saved alongside the originals using the naming scheme `hero-{name}.jpg`, `hero-{name}@2x.webp`, `thumb-{name}.jpg`, etc.
- **Environment variables** – None are required by default.  You could introduce a `.env` file and read it in `public/index.php` to configure database connections or base URLs when extending the project.

## Usage Examples

- **Visit the product list:** `GET /izdelki` – displays all products defined in `ProductRepository` with thumbnail images and short descriptions.
- **View a single product:** `GET /product/2` – shows a larger gallery and detailed description for the product with ID 2.  Use the thumbnail strip to switch images; the gallery is updated via the `initProductGallery()` JS module.
- **Upload a product image:**

  1. Visit `/admin/upload` to open the upload hub.  A `<select>` lists products by name/slug; you can filter using the search box.
  2. Select a product.  The panel shows the target directory (e.g. `/assets/product-images/headphones/`).
  3. Drag a JPG or PNG into the dropzone or click to choose a file.  The page sends a `POST /admin/upload/{slug}` request and displays the generated hero and thumb images.  The server returns JSON like:

     ```json
     {
       "ok": true,
       "slug": "headphones",
       "index": 5,
       "original": "/assets/product-images/headphones/5.jpg",
       "variants": {
         "hero": {
           "jpg": "/assets/product-images/headphones/hero-5.jpg",
           "jpg2": "/assets/product-images/headphones/hero-5@2x.jpg",
           "webp": "/assets/product-images/headphones/hero-5.webp",
           "webp2": "/assets/product-images/headphones/hero-5@2x.webp"
         },
         "thumb": {
           "jpg": "/assets/product-images/headphones/thumb-5.jpg",
           "jpg2": "/assets/product-images/headphones/thumb-5@2x.jpg",
           "webp": "/assets/product-images/headphones/thumb-5.webp",
           "webp2": "/assets/product-images/headphones/thumb-5@2x.webp"
         }
       }
     }
     ```

  You can find the newly uploaded originals and variants in the corresponding slug directory under `public/assets/product-images/`.

## Development Notes

- **Extending the product repository** – Currently products live in an array.  To connect to a database, replace `ProductRepository::all()` and `find()` with queries, and remove the in‑memory `$products`.  Consider using PDO or an ORM of your choice.  Ensure that `decorateImages()` still locates gallery images on disk.
- **Adding routes** – Use `$router->get()`, `$router->post()`, etc. in `public/index.php` to register new endpoints.  The route pattern syntax supports named parameters with optional regex (e.g. `/blog/{slug:[a-z\-]+}`).  Provide `[Controller::class, 'method']` as the handler.
- **Adding new pages** – Create a new view file under `src/Views/pages`, implement a controller method that calls `$this->render('pages/your-view', [...])`, and add a route pointing to it in `index.php`.
- **Switching image dimensions** – To change hero/thumb sizes, edit the hardcoded widths and heights in `ImageService::generateAll()` and re‑upload images.  You may also wish to update SCSS variables that define card and gallery dimensions.
- **Building assets** – `vite.config.js` defines multiple entry points: `app` (shared scripts), `style.scss` (global styles) and `admin-upload-hub` (admin page).  Output filenames are fixed for predictability.  If you add new pages with dedicated JS or SCSS, update the `input` section accordingly.

## Common Pitfalls

- **Missing `.vite-dev` file** – When working locally, remember to create a `.vite-dev` file in the project root **before** running `npm run dev`; otherwise `layout.php` will load the production assets, which might not exist yet.
- **Unwritable upload directory** – The web server must have write permission to `public/assets/product-images`.  Otherwise image uploads will fail with HTTP 500.
- **Image processing errors** – Intervention Image relies on PHP extensions (`gd` or `imagick`).  If neither is installed, `ImageService` will throw a “Failed to read image” or “No driver available” error.
- **Stale assets** – After updating SCSS or JS, run `npm run build` (for production) or restart the Vite dev server.  Otherwise the browser may continue to use stale cached files.
- **Large images** – Uploading very large PNGs can exhaust memory during variant generation.  Consider limiting upload file size via server configuration or modifying `ImageService` to resize images before processing.

## Future Extension Points

This scaffold is deliberately simple; it’s designed to be extended.  Possible enhancements include:

- **Database integration** – Replace the static product array with a relational or NoSQL database.  Add CRUD operations, search and pagination.
- **Authentication & authorisation** – Protect the admin upload section behind a login form and role checks.  Use sessions or JWTs to manage user state.
- **REST API** – Expose product data and image uploads via JSON API endpoints to support SPA or mobile clients.  You already have `POST /admin/upload/{slug}` returning JSON; follow a similar pattern.
- **Caching** – Introduce caching for product data and rendered views to improve performance.
- **Testing** – Add PHPUnit tests for router routing logic, controllers and the image service.  Use a virtual filesystem to test uploads.
- **Internationalisation (i18n)** – Extract strings into language files and allow runtime translation.
- **CI/CD pipeline** – Integrate linting (`phpstan`, `eslint`), unit tests and automatic asset builds into your deployment process.

---

_Generated README based on analysis of the provided codebase.  Some assumptions (e.g. how to initialise the project or configure environment) were made based on typical PHP/Vite setups._

---

