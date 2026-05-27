</main>
<footer>
    <?php \WpBlueprint\App\Core\Providers\MenuProvider::render('footer_menu'); ?>
    <p>&copy; <?= date('Y'); ?> <?= esc_html(get_bloginfo('name')); ?>. All rights reserved.</p>
</footer>
<?php wp_footer(); 

if (is_front_page()){
    echo '<script src="' . get_template_directory_uri() . '/assets/lib/slick/slick.min.js"></script>';
    echo '<script src="' . get_template_directory_uri() . '/assets/js/top.js"></script>';
};
?>
<script src="<?= get_template_directory_uri(); ?>/assets/js/script.js"></script>
<script src="<?= get_template_directory_uri(); ?>/assets/js/hls.js"></script>
<script src="<?= get_template_directory_uri(); ?>/assets/js/video.js"></script>
</body>
</html>
