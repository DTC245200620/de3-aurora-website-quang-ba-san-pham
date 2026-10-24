<?php get_header(); ?>
<section class="page-intro"><div class="container"><span class="kicker">KHÁM PHÁ AURORA</span><h1>Bộ sưu tập sản phẩm</h1><p>Thiết kế tinh tế cho những nhu cầu mỗi ngày.</p></div></section>
<section class="section"><div class="container"><div class="product-grid"><?php if(have_posts()): while(have_posts()): the_post(); aurora_card(); endwhile; else: ?><p>Chưa có sản phẩm.</p><?php endif; ?></div><?php the_posts_pagination(); ?></div></section>
<?php get_footer(); ?>
