<?php
namespace WpBlueprint\App\Models;

use WpBlueprint\App\Core\Support\BaseModel;

if (!defined('ABSPATH')) exit;

class TopPage extends BaseModel {
    protected static $post_type = 'kv_img';
}
