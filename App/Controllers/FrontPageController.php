<?php
namespace WpBlueprint\App\Controllers;

use WpBlueprint\App\Core\Support\BaseController;
use WpBlueprint\App\Models\News;
use WpBlueprint\App\Models\Shop;
use WpBlueprint\App\Models\TopPage;

if (!defined('ABSPATH')) exit;

class FrontPageController extends BaseController {

    public function __construct() {
        $this->index();
    }

    /**
     * Main front page method
     */
    public function index() {
        $news  = $this->news();
        $shops = $this->shop();
        $kvimg = $this->kvimg();

        $this->render('front-page.php', [
            'news'  => $news,
            'shops' => $shops,
            'kvimg' => $kvimg,
        ]);
    }

    /**
     * Get latest news posts as model objects
     */
    public function news(): News {
        return News::query()
            ->where('status', 'publish')
            ->limit(5); 
    }

    /**
     * Get latest shop posts as model objects
     */
    public function shop(): Shop {
        return Shop::query()
            ->where('status', 'publish')
            ->limit(5);
    }

    /**
     * Get latest kvimg posts as model objects
     */
    public function kvimg(): TopPage {
        return TopPage::query()
            ->where('status', 'publish')
            ->with(['acf_image', 'acf_url' , 'acf_target'])
            ->limit(9);
    }
}
