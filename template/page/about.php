<?php get_template_part('template/components/header'); ?>
<?php
use function WpBlueprint\breadcrumb;
?>
<!--BreadCrumb-->

<!-- Default -->
<p class="breadcrumb-wrapper"><?= breadcrumb(); ?></p>

<!-- Override label only, home link still works -->
<p class="breadcrumb-wrapper">
    <?= breadcrumb([
        ['label' => 'Toppage'],  // still links to home_url('/')
        ['label' => 'Aboutpage']  // no link, just text
    ]); ?>
</p>

<!-- Override label and custom link -->
<p class="breadcrumb-wrapper">
    <?= breadcrumb([
        ['label' => 'Toppage'], // still home link
        ['label' => 'Custom About', 'link' => '/about-page'], // link to custom page
        ['label' => 'Custom About inner', 'link' => '/about-page/inner/'] // link to custom page
    ]); ?>
</p>

<!--/BreadCrumb-->

<div class="container">
    <!-- News Section -->
    <section class="about">
        test about test
    </section>
</div>

<?php get_template_part('template/components/footer'); ?>
