<?php
namespace WpBlueprint\App\Controllers;

use WpBlueprint\App\Models\Shop;
use WpBlueprint\App\Core\Support\BaseController;

if (!defined('ABSPATH')) exit;

class FloorguideController extends BaseController {

    public function __construct() {
        $this->index();
    }

    protected function index() {
        $shops = Shop::query()
            ->where('status', 'publish')
            ->limit(5);

        $this->render('page/floorguide.php', compact('shops'));
    }
}
