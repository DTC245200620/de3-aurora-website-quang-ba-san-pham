<!doctype html>
<html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<a class="skip" href="#main">Đến nội dung</a>
<header class="header"><div class="container header-inner">
<a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><span class="brand-mark" aria-hidden="true">A</span><span><b>Aurora</b><small>THIẾT KẾ CHO CUỘC SỐNG</small></span></a>
<button class="menu-toggle" aria-controls="navigation" aria-expanded="false" aria-label="Mở menu">☰</button>
<nav id="navigation" aria-label="Điều hướng chính">
<a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a><a href="<?php echo esc_url(get_post_type_archive_link('aurora_product')); ?>">Sản phẩm</a><a href="<?php echo esc_url(home_url('/gioi-thieu/')); ?>">Giới thiệu</a><a class="nav-contact" href="<?php echo esc_url(home_url('/lien-he/')); ?>">Liên hệ <span aria-hidden="true">↗</span></a>
</nav></div></header>
<main id="main">
