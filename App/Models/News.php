<?php
namespace WpBlueprint\App\Models;

use WpBlueprint\App\Core\Support\BaseModel;

if (!defined('ABSPATH')) exit;

class News extends BaseModel {
    protected static $post_type = 'news';
}
