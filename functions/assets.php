<?php
/**
 * Set up theme defaults
 */
function wd_setup_theme() {
    // title
    add_theme_support( 'title-tag' );
    // Add support for default features
    // add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'post-thumbnails' );
    // add_theme_support( 'post-formats',  array ( 'aside', 'gallery', 'quote', 'image', 'video' ) );

    // Add support for Woocommerce
    if( class_exists( 'WooCommerce' ) ) {
        add_theme_support( 'woocommerce' );
        add_theme_support( 'wc-product-gallery-zoom' );
        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-slider' );
    }

    // Register Navigation Menus
    register_nav_menus(
        array(
            'primary'   => __( 'Primary Menu', 'wd_theme' ),
            'secondary' => __( 'Secondary Menu', 'wd_theme' ),
        )
    );
}
add_action( 'after_setup_theme', 'wd_setup_theme' );

/**
 * Hide WP version
 */
function remove_wp_version_rss() {
    return '';
}
function sdt_remove_ver_css_js( $src, $handle ) {
    $handles_with_version = ['style']; // <-- Adjust to your needs!

    if( strpos( $src, 'ver=' ) && ! in_array( $handle, $handles_with_version, true ) )
        $src = remove_query_arg( 'ver', $src );

    return $src;
}
add_filter( 'the_generator', 'remove_wp_version_rss' );
add_filter( 'style_loader_src',  'sdt_remove_ver_css_js', 9999, 2 );
add_filter( 'script_loader_src', 'sdt_remove_ver_css_js', 9999, 2 );

/**
 * Remove WordPress junk found on header
 */
// Emoji
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
// RSS Feed
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
// REST API
remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
// Others
remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
remove_action( 'wp_head', 'wp_shortlink_wp_head');
remove_action( 'wp_head', 'wlwmanifest_link');
remove_action( 'wp_head', 'wp_generator');
// Embed
function wd_deregister_scripts() {
	wp_deregister_script( 'wp-embed' );
}
add_action( 'wp_footer', 'wd_deregister_scripts' );

/**
 * Custom Shortcodes
 */
add_shortcode( 'url', 'get_site_url' );
add_shortcode( 'stylesheet_directory', 'get_styesheet_directory_uri' );

/**
 * Enqueue styles and scripts
 */
function wd_styles_scripts() {
    // Remove default WP styles
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-block-style' ); // REMOVE WOOCOMMERCE BLOCK CSS
    wp_dequeue_style( 'global-styles' ); // REMOVE THEME.JSON


    // Styles
    // wp_enqueue_style('swiper-bundle_style', get_stylesheet_directory_uri() . '/assets/lib/swiper/swiper-bundle.min.css');


    // Scripts
    // wp_enqueue_script('swiper-bundle_script', get_stylesheet_directory_uri() . '/assets/lib/swiper/swiper-bundle.min.js', array(), false, true);
    // wp_enqueue_script('gsap_script', get_stylesheet_directory_uri() . '/assets/lib/gsap/gsap.min.js', array(), false, true);
    // wp_enqueue_script('scrolltrigger_script', get_stylesheet_directory_uri() . '/assets/lib/gsap/ScrollTrigger.min.js', array(), false, true);


    // Main style / script
    wp_enqueue_style( 'site_style', get_stylesheet_directory_uri() . '/assets/css/style.css' );
    // wp_enqueue_script( 'site_script', get_stylesheet_directory_uri() . '/assets/js/scripts.min.js', array( 'jquery' ), false, true );

    // if ( is_front_page() ) {
    //     wp_enqueue_script( 'top_script', get_stylesheet_directory_uri() . '/assets/js/top.min.js', array( 'jquery' ), false, true );
    // }
    // if ( is_front_page() || is_page('company') ) {
    //     wp_enqueue_script( 'slider_script', get_stylesheet_directory_uri() . '/assets/js/slider.min.js', array( 'jquery' ), false, true );
    // }
}
add_action( 'wp_enqueue_scripts', 'wd_styles_scripts' );

/**
 * Adds Browser and OS types to <body> as class
 */
function wd_browsing_type( $classes ) {
    global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

    if( $is_lynx ) $classes[] = 'lynx';
    elseif( $is_gecko ) $classes[] = 'gecko';
    elseif( $is_opera ) $classes[] = 'opera';
    elseif( $is_NS4 ) $classes[] = 'ns4';
    elseif( $is_safari ) $classes[] = 'safari';
    elseif( $is_chrome ) $classes[] = 'chrome';
    elseif( $is_IE ) {
        $classes[] = 'ie';
        if( preg_match( '/MSIE ([0-9]+)([a-zA-Z0-9.]+)/', $_SERVER['HTTP_USER_AGENT'], $browser_version ) )
            $classes[] = 'ie' . $browser_version[1];
    } else $classes[] = 'unknown';

    if( $is_iphone ) $classes[] = 'iphone';
    
    if( stristr( $_SERVER['HTTP_USER_AGENT'], 'mac' ) ) {
        $classes[] = 'osx';
    } elseif ( stristr( $_SERVER['HTTP_USER_AGENT'], 'linux' ) ) {
        $classes[] = 'linux';
    } elseif ( stristr( $_SERVER['HTTP_USER_AGENT'], 'windows' ) ) {
        $classes[] = 'windows';
    }

    return $classes;
}
add_filter( 'body_class', 'wd_browsing_type' );

/**
 * Remove empty HTML tags when using the_content()
 */
function wd_remove_empty_html_tags( $str, $repto = NULL ) {

    $str = force_balance_tags($str);
   
    if( ! is_string( $str )|| '' == trim( $str ) ) return $str;

    return preg_replace (
        '/<([^<\/>]*)>([\s]*?|(?R))<\/\1>/imsU',
        !is_string( $repto ) ? '' : $repto,
        $str
    );
}
add_filter( 'the_content', 'wd_remove_empty_html_tags', 20, 1 );

/**
 * JAPAN TEAM (メニューに◯◯◯を追加)
 * Add custom menu links
 */
function wd_menu_pages () {
    // マニュアル
    add_menu_page( '更新マニュアル', '更新マニュアル', 'manage_options', 'manual' );

    // TeamViewer
    add_menu_page( 'TeamViewer', 'TeamViewer', 'manage_options', 'teamviewer' );
}
add_action ( 'admin_menu', 'wd_menu_pages', 1000 );

/**
 * JAPAN TEAM (メニューのリンク先変更)
 * Custom menu link scripts
 */
function wd_menu_pages_links() {
    $pdf_url = get_template_directory_uri() . '/pdf/manual.pdf';
?>
    <script type="text/javascript">
        // マニュアル
        jQuery( function( $ ) {
            // $("#toplevel_page_manual a").attr("href", "<?php // echo $pdf_url; ?>");
            // $("#toplevel_page_manual a").attr("target", "_blank");
            $('#toplevel_page_manual a').prop({
                href: '<?php echo $pdf_url; ?>',
                target: '_blank'
            });
        });
        
        // TeamViewer
        jQuery( function( $ ) {
            $('#toplevel_page_teamviewer a').prop({
                href: 'https://www.teamviewer.com/ja/',
                target: '_blank'
            });
        });
    </script>
<?php
}
add_action( 'admin_footer', 'wd_menu_pages_links' );

/**
 * JAPAN TEAM (管理画面のWordPressロゴを非表示にする)
 * Hide admin logo
 */
function wd_hide_admin_logo() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'wp-logo' );
}
add_action( 'wp_before_admin_bar_render', 'wd_hide_admin_logo' );

/**
 * JAPAN TEAM (投稿記事のスラッグが日本語などマルチバイトの場合は、{投稿タイプ}-{記事ID}に強制的に変更)
 * Change post slug to post_type-post_id
 */
function wd_auto_post_slug( $slug, $post_ID, $post_status, $post_type ) {
    if( 'page' !== $post_type || preg_match( '/(%[0-9a-f]{2})+/', $slug ) ) {
        $slug = utf8_uri_encode( $post_type ) . '-' . $post_ID;
    }
    return $slug;
}
add_filter( 'wp_unique_post_slug', 'wd_auto_post_slug', 10, 4 );


/**
 * Disable WPCF7 features
 */
if ( class_exists( 'WPCF7' ) ) {
    add_filter( 'wpcf7_autop_or_not', '__return_false' );
    add_filter( 'wpcf7_load_js', '__return_false' );
}


function custom_wpcf7_validate_kana($result,$tag)
{
    $tag   = new \WPCF7_FormTag($tag);
    $name  = $tag->name;
    $value = isset($_POST[$name]) ? trim(wp_unslash(strtr((string) $_POST[$name], "\n", " "))) : "";

    //全角カタカナ又は平仮名の入力チェック
    if ($name === "name-kana") {
        if(!preg_match("/^[ア-ヶーぁ-ん\s　]+$/u", $value)){
            $result->invalidate( $tag,"全角カタカナ又は平仮名で入力してください。");
        }
    }

    return $result;
}
add_filter('wpcf7_validate_text', __NAMESPACE__ . '\custom_wpcf7_validate_kana', 11, 2);
add_filter('wpcf7_validate_text*', __NAMESPACE__ . '\custom_wpcf7_validate_kana', 11, 2);

?>