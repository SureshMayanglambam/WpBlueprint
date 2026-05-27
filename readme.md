############### ENGLISH
**Purpose**

This WordPress theme is structured in OOP style, with:
・Controllers for dynamic pages / CPTs
・Templates separated into `front-page, single, archive, page, and reusable components`
・Optional debug overlay for development `(THEME_DEBUG)`


**Test Post Types**

For development/testing purposes, the following custom post types (CPTs) were temporarily added:
| CPT Name | Usage | 
| -------- | -------- |
| news | Used to test `NewsController`, archive, and single template rendering |
| shop | Used to test `ShopController`, archive, and single template rendering |

⚠️ Note: These CPTs are only for development/testing.
You can safely remove them when they are no longer needed.


**Pages**

Static pages without logic: just create a file in `/template/page/{slug}.php`
Pages with logic: create a corresponding controller in `/Controller/` and handle data rendering via render() method.


**Debug Overlay**

Controlled by constants:
---------------------------
define('WP_DEBUG', true); 
define('THEME_DEBUG', true);  
---------------------------
Shows controller, template, and data passed.
Turn off in production by setting `THEME_DEBUG` to `false`.


**Autoload & Routing**

・Controllers are auto-loaded via functions.php using PHP autoloader.
・Routing logic example:
-----------------------------------------
if (is_front_page()) {
    new \WpBlueprint\App\Controller\FrontPageController();
} elseif (is_singular('news') || is_post_type_archive('news')) {
    new \WpBlueprint\App\Controller\NewsController();
} elseif (is_singular('shop') || is_post_type_archive('shop')) {
    new \WpBlueprint\App\Controller\ShopController();
} elseif (is_page('floorguide')) {
    new \WpBlueprint\App\Controller\FloorguideController();
} elseif (is_page()) {
    $page = get_post();
    $slug = $page ? $page->post_name : 'page';
    $template = locate_template("template/page/{$slug}.php");
    if ($template) include $template;
}
-----------------------------------------


**Template Organization**
-------------------------------------------
template/
├── front-page.php
├── single/
│   ├── news.php
│   └── shop.php
├── archive/
│   ├── news.php
│   └── shop.php
├── page/
│   ├── about.php
│   └── floorguide.php
└── components/
    ├── header.php
    └── footer.php
---------------------------------------------
use header and footer path will be wherever you want.




############### JAPANESE
**目的** 
このWordPressテーマはOOPスタイルで構築されています：
・動的ページやカスタム投稿タイプ（CPT）のためのコントローラー
・テンプレートは `front-page, single, archive, page、再利用可能な components に分離`
・開発用にオプションのデバッグオーバーレイ`（THEME_DEBUG）`


 **テスト用カスタム投稿タイプ** 
 
 開発・テスト目的で以下のカスタム投稿タイプ（CPT）を一時的に追加しました：
 | CPT名 | 用途 | 
 | ------ | -------- | 
 | news | NewsController、アーカイブ、シングルテンプレートのレンダリングをテストするため |
 | shop | ShopController、アーカイブ、シングルテンプレートのレンダリングをテストするため | 
 
 ⚠️ 注意: これらのCPTは開発・テスト専用です。
 不要になったら安全に削除可能です。 
 
 
 **ページ** 
 
 - ロジック不要の静的ページ: `/template/page/{slug}.php` にファイルを作成するだけ 
 - ロジックが必要なページ: `/Controller/` に対応するコントローラーを作成し、`render()` メソッドでデータを渡す 
 
 
 **デバッグオーバーレイ** 
 
 定数で制御可能：
---------------------------------------------
  define('WP_DEBUG', true); 
  define('THEME_DEBUG', true); 
---------------------------------------------
  
  コントローラー、テンプレート、渡されたデータを表示。本番では `THEME_DEBUG` を `false` に設定してオフに可能 
  
  
**オートロード & ルーティング** 
  
コントローラーは `functions.php` のPHPオートローダーで自動ロード。ルーティング例：
---------------------------------------------
if (is_front_page()) { 
    new \WpBlueprint\App\Controller\FrontPageController(); 
}
elseif (is_singular('news') || is_post_type_archive('news')) { 
    new \WpBlueprint\App\Controller\NewsController(); 
} 
elseif (is_singular('shop') || is_post_type_archive('shop')) {
    new \WpBlueprint\App\Controller\ShopController(); 
} 
elseif (is_page('floorguide')) { 
    new \WpBlueprint\App\Controller\FloorguideController(); 
} 
elseif (is_page()) { 
    $page = get_post(); 
    $slug = $page ? $page->post_name : 'page'; 
    $template = locate_template("template/page/{$slug}.php"); 
    if ($template) include $template; }
---------------------------------------------
          


**テンプレート構成** 
---------------------------------------------
template/
 ├── front-page.php
 ├── single/ 
 │   ├── news.php 
 │   └── shop.php 
 ├── archive/ 
 │   ├── news.php 
 │   └── shop.php 
 ├── page/ 
 │   ├── about.php 
 │   └── floorguide.php 
 └── components/     
    ├── header.php     
    └── footer.php 
---------------------------------------------
ヘッダーやフッターのパスは任意の場所に置いて構いません。

