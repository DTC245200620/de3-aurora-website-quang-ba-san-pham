<?php get_header(); while(have_posts()): the_post(); ?>
<section class="page-intro"><div class="container"><span class="kicker">AURORA</span><h1><?php the_title(); ?></h1></div></section>
<section class="section"><div class="container prose"><?php the_content(); ?>
<?php if(is_page('lien-he')): ?><div class="contact-card"><h2>Cùng tìm lựa chọn phù hợp</h2><p>Email: <a href="mailto:dtc245200620@ictu.edu.vn">dtc245200620@ictu.edu.vn</a></p><p>Gửi tên sản phẩm hoặc mã SKU bạn quan tâm để nhận tư vấn.</p></div><?php endif; ?>
<a class="button primary" href="<?php echo esc_url(get_post_type_archive_link('aurora_product')); ?>">Khám phá sản phẩm ↗</a></div></section>
<?php endwhile; get_footer(); ?>
