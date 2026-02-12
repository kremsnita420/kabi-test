# Minimal MVC (Hardened) + SCSS/JS Build Pipeline (Vite) — Interview Task Scaffold

This repository is a **small, framework-free PHP 8 application** built to match a typical “design → list page + detail page” interview assignment.

It intentionally balances two things:
- **Simplicity** (the scope is tiny: 5 products + detail page)
- **Professional structure** (clean routing, MVC separation, safe rendering, build pipeline)

The result runs on:
- **Apache 2.4** (with `mod_rewrite`)
- **PHP 8.x**
- Optional: **Node.js** for building SCSS/JS (prebuilt assets are included)

---

## What the task asked for (mapped to implementation)

### ✅ “In PHP (+CSS/SCSS, JS) build a page and subpage from the design”
- PHP renders HTML views via a minimal MVC pattern.
- Styling is authored in **SCSS** (split into partials) and compiled to CSS.
- JS is organized by **Separation of Concerns (SOC)** into small modules.

### ✅ “Product list page with 5 products”
- `/products` renders the list of **exactly 5** in-memory products.

### ✅ “Clicking ‘Več’ opens a product subpage”
- Each card links to `/product/{id}`.
- `/product/1` renders a single product page.

### ✅ “May use frameworks/libraries / template system”
- We deliberately **did not** add a heavy PHP framework (Laravel/Symfony), because the scope is tiny and interviewers often penalize overkill.
- Instead, we used:
  - A **minimal router**
  - A **simple layout + view system**
  - **Composer PSR-4 autoloading** (professional, lightweight)

### ✅ “Responsive; mobile under 500px”
- SCSS includes a dedicated `@mixin mobile` breakpoint at **500px**.

### ✅ “Must run on Apache 2.4 with PHP 8 (and .htaccess allowed)”
- Apache rewrite rules route all requests to `public/index.php`.
- No reliance on nginx-only features.

---

## Why we used this architecture (and what it demonstrates)

Interview tasks like this rarely test “can you render 5 products.”
They test whether you can build something that is:
- maintainable,
- readable,
- secure by default,
- and easy to extend.

This scaffold demonstrates exactly that, without bloat.

### 1) Front Controller (single entry point)
All requests go through:
- `public/index.php`

Why:
- Central place to bootstrap dependencies and route requests
- Predictable request flow
- Standard practice in modern PHP apps

### 2) Clean routing (no giant `if/else` chains)
`src/Core/Router.php` maps URL paths to controllers:

- `GET /products` → `ProductController::index`
- `GET /product/{id}` → `ProductController::show`

Why:
- Keeps URL mapping in one place
- Easy to add new pages later
- Makes code review and maintenance easier

### 3) Minimal MVC separation
- **Model**: `src/Models/ProductRepository.php` (data source)
- **Controller**: `src/Controllers/ProductController.php` (logic + orchestration)
- **Views**: `src/Views/*.php` (presentation)

Why:
- Business logic doesn’t leak into templates
- Templates stay focused on rendering
- Controller logic stays testable and readable

### 4) PSR-4 autoloading via Composer
`composer.json` defines:

```json
"autoload": { "psr-4": { "App\\": "src/" } }
```

Why:
- No manual `require_once` spaghetti
- Standard PHP ecosystem practice
- Makes namespacing “just work”

### 5) Hardened input handling
In `ProductController::show(string $id)`:
- We validate the path segment with `ctype_digit($id)`
- We cast to int only after validation
- We return 404 when invalid or missing

Why:
- Prevents accidental type confusion
- Avoids exposing internals on malformed URLs
- Demonstrates defensive programming

### 6) Basic XSS hygiene (escaping output)
Templates use `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.

Why:
- Output escaping is one of the simplest high-value security measures
- Even if data is “internal,” demonstrating safety-first habits scores points

---

## Frontend build: why Vite (instead of Webpack)

We chose **Vite** because it is:
- faster to set up,
- less configuration-heavy than Webpack,
- still industry-standard,
- perfect for a small interview repo.

Webpack is powerful, but for this task it adds complexity with little benefit.

### Important detail: stable filenames (no hashes)
The Vite config outputs:
- `public/assets/css/style.css`
- `public/assets/js/app.js`

Why:
- PHP templates can reference predictable file paths
- No manifest or asset versioning needed for a small task
- Still “production-like” without extra moving parts

### Prebuilt assets included
The repository includes compiled files in `public/assets/*` so it runs even if the reviewer does **not** install Node.

Why:
- Interviewers sometimes only run PHP/Apache locally
- This avoids “it doesn’t work on my machine” friction
- You still provide the build pipeline for correctness

---

## SCSS organization (best-practice split)

SCSS follows a clean split inspired by the “7–1” pattern:

```
resources/scss/
  style.scss                # Entry (imports only)
  abstracts/_variables.scss  # tokens (colors, spacing, breakpoints)
  abstracts/_mixins.scss     # reusable mixins (mobile breakpoint)
  base/_reset.scss           # minimal reset / box sizing
  base/_typography.scss      # font + base sizing
  layout/_header.scss        # layout-level header rules
  components/_grid.scss      # reusable grid component
  components/_card.scss      # reusable card component
  pages/_products.scss       # page-specific rules (kept minimal)
  utilities/_helpers.scss    # helper classes (.hidden etc.)
```

Why:
- Scales cleanly as UI grows
- Encourages reusable components
- Prevents “one giant style.css” chaos

---

## JS organization (Separation of Concerns)

JS is split by responsibility:

```
resources/js/
  app.js                # Entry point
  core/dom.js            # DOM primitives (qs/qsa)
  modules/products.js    # feature module (products page behavior)
  utils/logger.js        # small utility wrapper
```

Why:
- Each file has a single purpose
- Easy to test/replace
- Avoids “one giant app.js” as the project grows

For the interview task, JS is intentionally minimal.
We include structure without inventing fake features.

---

## Project structure overview

```
public/
  index.php              # Front controller
  .htaccess              # Apache rewrite
  assets/
    css/style.css        # compiled output (prebuilt)
    js/app.js            # compiled output (prebuilt)

src/
  Core/
    Router.php           # routing + dispatch
    Controller.php       # base controller (render helper)
  Controllers/
    ProductController.php
    NotFoundController.php
  Models/
    ProductRepository.php
  Views/
    layout.php
    product-list.php
    product-single.php

resources/
  scss/                  # SCSS source (split structure)
  js/                    # JS source (SOC structure)

vite.config.js
package.json
composer.json
README.md
```

---

## Running the project

### 1) PHP/Apache
1. Install dependencies (autoload):
   ```bash
   composer dump-autoload
   ```

2. Point Apache **DocumentRoot** to `public/`.

3. Ensure Apache allows `.htaccess` overrides:
   - `AllowOverride All`
   - `mod_rewrite` enabled

Open:
- `http://localhost/products`
- `http://localhost/product/1`

### 2) Build assets (optional, but recommended if you change SCSS/JS)
1. Install Node deps:
   ```bash
   npm install
   ```

2. Build:
   ```bash
   npm run build
   ```

Outputs:
- `public/assets/css/style.css`
- `public/assets/js/app.js`

---

## Design decisions (in plain interview language)

- **No DB**: the task didn’t ask; in-memory repository is enough and keeps focus on structure.
- **No heavy PHP framework**: demonstrates you can architect cleanly without scaffolding doing it for you.
- **Autoloading + namespaces**: shows modern PHP practices.
- **Defensive routing + 404 handling**: shows robustness.
- **Split SCSS/JS**: shows you understand maintainability, not just “make it work.”
- **Prebuilt assets included**: reduces reviewer friction.

---

## Extending (if asked in interview)
This scaffold can grow without refactor pain:
- Add new pages by adding route + controller + view
- Swap in Twig later if needed
- Replace repository with database layer (PDO/Doctrine) cleanly
- Add asset versioning if project becomes larger

---

## Quick checklist (what to mention during interview)
- Front controller + clean routing
- MVC separation
- PSR-4 autoloading
- Output escaping (XSS hygiene)
- Mobile breakpoint under 500px
- Vite chosen over Webpack for simplicity + speed
- Stable asset filenames to keep PHP templates simple
- Prebuilt assets shipped to avoid environment issues

---

## Dev workflow (live SCSS updates / HMR)

### Why changes didn't reflect before
When you open the app via Apache/PHP (e.g. `/products`), the template normally loads **built** files from:
- `/public/assets/css/style.css`
- `/public/assets/js/app.js`

Running `npm run dev` starts a **separate** Vite dev server on `http://localhost:5173`, but your PHP templates won't use it unless you explicitly load Vite assets.

### How dev mode works here
- `resources/js/app.js` imports SCSS: `import "../scss/style.scss";`
- `src/Views/layout.php` switches between:
  - **Vite dev server** (HMR/live reload) when `VITE_DEV=1`
  - **built assets** when not in dev

### Start dev mode
1. Start Vite:
   ```bash
   VITE_DEV=1 npm run dev
   ```

2. Open the app via Apache as usual:
   - `/products`
   - `/product/1`

SCSS changes should now reflect instantly.

### If `VITE_DEV` is not visible in PHP
Some Apache/PHP setups don't inherit shell environment variables.
If `getenv('VITE_DEV')` doesn't work for you, quickest workaround for interview tasks:
- temporarily change `$viteDev` in `layout.php` to `true`
- or implement a small flag file check (e.g. `file_exists(__DIR__ . '/../../.vite-dev')`)

---
