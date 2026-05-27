# WpBlueprint

A clean, OOP-structured WordPress starter theme.  
Built with Controllers, Models, a Router, and a Debug overlay — no Composer, no dependencies.

---

## Features

- **OOP architecture** — Controllers, Models, and a Router instead of scattered template files
- **Fluent query builder** — `News::query()->where('status', 'publish')->paginate(10)`
- **Auto-routing** — register CPTs and pages in one file (`routes/web.php`)
- **Auto-loader** — PSR-style class loading via `spl_autoload_register`, no Composer needed
- **ACF support** — load ACF fields per post with `.with(['field_name'])`
- **Taxonomy & meta filters** — `filterByTax()` and `filterBy()` built into the model
- **Pagination** — rendered automatically by `paginate()`
- **Breadcrumbs** — auto-generated based on WP context, with optional override
- **Debug overlay** — shows Controller, Template, and Data when `THEME_DEBUG` is true
- **Menu provider** — render nav menus by location with `MenuProvider::render('primary')`
- **Security** — removes WP version, emoji scripts, RSS links, and REST API head links
- **WooCommerce ready** — theme support added automatically if WooCommerce is active

---

## Quick Install

```bash
bash <(curl -s https://raw.githubusercontent.com/SureshMayanglambam/WpBlueprint/main/install.sh)
```

The wizard will ask:

| Prompt | Example |
|---|---|
| Path to `wp-content/themes/` | `/var/www/html/wp-content/themes` |
| Theme folder name | `wp-blueprint` |
| Rename namespace? | Optional — enter your own PascalCase namespace |

---

## Requirements

- PHP 8.0 or higher
- WordPress 6.0 or higher
- ACF (Advanced Custom Fields) — optional, gracefully skipped if not active

---

## Folder Structure

```
WpBlueprint/
├── install.sh                        ← CLI installer
├── functions.php                     ← Autoloader + provider bootstrapper
├── style.css                         ← Theme header
├── index.php                         ← Entry point
├── routes/
│   └── web.php                       ← ★ Register your routes here
├── App/
│   ├── Controllers/
│   │   └── NewsController.php        ← Example controller (archive + single)
│   ├── Models/
│   │   └── News.php                  ← Example model
│   └── Core/
│       ├── Support/
│       │   ├── BaseController.php    ← render(), getPaginatedPosts()
│       │   └── BaseModel.php         ← Fluent query builder
│       ├── Helpers/
│       │   ├── Breadcrumb.php        ← Auto breadcrumb generator
│       │   ├── Debug.php             ← Debug overlay
│       │   └── Pagination.php        ← Pagination renderer
│       └── Providers/
│           ├── Router.php            ← Routing engine (do not edit)
│           ├── RouteServiceProvider.php
│           ├── MenuProvider.php      ← Nav menu renderer
│           └── SimpleDebugProvider.php
├── template/
│   ├── front-page.php
│   ├── archive/
│   │   └── news.php                  ← Example archive template
│   ├── single/
│   │   └── news.php                  ← Example single template
│   ├── page/                         ← Static pages by slug (auto-served)
│   └── components/
│       ├── header.php
│       └── footer.php
└── functions/
    ├── assets.php                    ← Theme setup, enqueue, WP hooks
    └── helpers.php                   ← Global breadcrumb() helper
```

---

## How It Works

### 1. Register routes — `routes/web.php`

```php
// Front page
Router::frontPage(FrontPageController::class);

// Custom post type (handles archive + single)
Router::postType('news', NewsController::class);

// Static page by slug
Router::page('about', AboutController::class);

// Fallback: auto-serves template/page/{slug}.php
Router::pageFallback();
```

### 2. Create a Model — `App/Models/YourModel.php`

```php
namespace WpBlueprint\App\Models;

use WpBlueprint\App\Core\Support\BaseModel;

class Product extends BaseModel {
    protected static $post_type = 'product';
}
```

### 3. Create a Controller — `App/Controllers/YourController.php`

```php
namespace WpBlueprint\App\Controllers;

use WpBlueprint\App\Core\Support\BaseController;
use WpBlueprint\App\Models\Product;

class ProductController extends BaseController {

    public function __construct() {
        if (is_post_type_archive('product')) {
            $this->index();
        } elseif (is_singular('product')) {
            $this->show();
        }
    }

    protected function index() {
        $products = Product::query()
            ->where('status', 'publish')
            ->paginate(12);

        $this->render('archive/product.php', compact('products'));
    }

    protected function show() {
        $product = Product::find(get_the_ID());
        $this->render('single/product.php', compact('product'));
    }
}
```

### 4. Create a Template — `template/archive/product.php`

```php
<?php get_template_part('template/components/header'); ?>

<div class="container">
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $post): ?>
            <article>
                <a href="<?= esc_url($post->url) ?>"><?= esc_html($post->title) ?></a>
                <small><?= esc_html($post->date) ?></small>
            </article>
        <?php endforeach; ?>
        <?= $products->pagination ?? '' ?>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>
</div>

<?php get_template_part('template/components/footer'); ?>
```

---

## Static Pages (No Controller)

For pages that don't need data — just create a file in `template/page/` matching the page slug:

```
template/page/about.php
template/page/contact.php
template/page/faq.php
```

WordPress will serve them automatically via `Router::pageFallback()`.

---

## Model Query Reference

```php
// Basic
Product::query()->where('status', 'publish')->paginate(12);
Product::query()->limit(5);
Product::find($id);

// With ACF fields
Product::query()->with(['acf_image', 'acf_price'])->paginate(12);

// Filter by ACF radio/select from $_GET
Product::query()->filterBy('acf_category')->paginate(12);

// Filter by taxonomy from $_GET
News::query()->filterByTax('news-category')->paginate(10);

// Meta filter
Product::query()->withMeta('acf_price', '1000', '>')->paginate(12);

// Get ACF choices for a filter UI
$categories = Product::categories('acf_category');

// Get taxonomy terms for a filter UI
$terms = News::terms('news-category');
```

---

## Debug Overlay

Enable in `wp-config.php`:

```php
define('WP_DEBUG', true);
define('THEME_DEBUG', true);
```

Shows a fixed bar at the bottom of every page with:
- Current **Controller** class
- **Template** file being rendered
- **Data** passed to the template

Disable in production:

```php
define('THEME_DEBUG', false);
```

---

## Breadcrumbs

In any template:

```php
use function WpBlueprint\breadcrumb;

// Auto-generated
echo breadcrumb();

// Custom labels
echo breadcrumb([
    ['label' => 'Home'],
    ['label' => 'Products', 'link' => '/products'],
    ['label' => 'Current Product'],
]);
```

---

## Nav Menus

Register locations in `functions/assets.php`, then render in templates:

```php
// In functions/assets.php
register_nav_menus([
    'primary'     => 'Primary Menu',
    'footer_menu' => 'Footer Menu',
]);

// In templates
MenuProvider::render('primary');
MenuProvider::render('footer_menu');
```

---

## Manual Install (without the installer)

```bash
cd /your/wordpress/wp-content/themes
git clone https://github.com/SureshMayanglambam/WpBlueprint.git
```

Then activate the theme in WP Admin → Appearance → Themes.

---

## License

MIT — free to use, modify, and distribute.
