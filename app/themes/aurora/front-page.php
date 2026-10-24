<?php get_header(); $products=new WP_Query(['post_type'=>'aurora_product','posts_per_page'=>6,'orderby'=>'menu_order','order'=>'ASC']); ?>
<section class="hero"><div class="container hero-grid"><div class="hero-copy">
<span class="eyebrow"><i></i> SẢN PHẨM CHẤT LƯỢNG</span>
<h1>Khám phá sản phẩm<br>phù hợp với <em>phong cách</em><br>của bạn</h1>
<p>Bộ sưu tập sản phẩm được chọn lọc với thiết kế hiện đại, thông tin rõ ràng và trải nghiệm trực quan.</p>
<div class="actions"><a class="button primary" href="#san-pham">Khám phá sản phẩm ↗</a><a class="button secondary" href="<?php echo esc_url(home_url('/gioi-thieu/')); ?>">Về Aurora</a></div>
<div class="proof"><span>✓ Thiết kế tinh tế</span><span>✓ Chất lượng chọn lọc</span><span>✓ Trải nghiệm dễ dàng</span></div>
</div><div class="hero-visual">
<div class="visual-halo"></div>
<?php $v=0; while($products->have_posts() && $v<3): $products->the_post(); $v++; ?><a class="floating float-<?php echo (int)$v; ?>" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large',['loading'=>'eager']); ?><div><strong><?php the_title(); ?></strong><span><?php echo esc_html(aurora_price()); ?></span></div></a><?php endwhile; wp_reset_postdata(); ?>
<span class="visual-note">✦ Chọn điều phù hợp với bạn</span>
</div></div></section>
<section class="section collection" id="san-pham"><div class="container"><div class="section-heading"><div><span class="kicker">BỘ SƯU TẬP AURORA</span><h2>Sản phẩm nổi bật</h2><p>Những lựa chọn nhỏ, nâng tầm trải nghiệm mỗi ngày.</p></div><a class="text-link" href="<?php echo esc_url(get_post_type_archive_link('aurora_product')); ?>">Xem tất cả sản phẩm ↗</a></div><div class="product-grid"><?php $products->rewind_posts(); while($products->have_posts()): $products->the_post(); aurora_card(); endwhile; wp_reset_postdata(); ?></div></div></section>
<section class="section about-strip"><div class="container about-grid"><div><span class="kicker">CÂU CHUYỆN AURORA</span><h2>Đơn giản hơn.<br>Thiết thực hơn.<br><em>Đậm chất riêng.</em></h2></div><div><p class="lead">Chúng tôi tin rằng những sản phẩm tốt sẽ khiến cuộc sống hàng ngày trở nên dễ chịu hơn.</p><p>Từ góc làm việc đến những chuyến đi, Aurora lựa chọn những thiết kế kết hợp sự tinh tế với công năng thực tế. Khám phá sản phẩm phù hợp với nhịp sống của bạn.</p><a class="text-link" href="<?php echo esc_url(home_url('/gioi-thieu/')); ?>">Tìm hiểu về Aurora ↗</a></div></div></section>
<section class="section"><div class="container benefits"><div><span>01</span><h3>Thiết kế có chủ đích</h3><p>Đường nét gọn gàng, màu sắc hài hòa và dễ dàng kết hợp.</p></div><div><span>02</span><h3>Chất lượng chọn lọc</h3><p>Chú trọng trải nghiệm sử dụng và những chi tiết nhỏ.</p></div><div><span>03</span><h3>Đồng hành mỗi ngày</h3><p>Những lựa chọn thiết thực cho công việc và cuộc sống.</p></div></div></section>
<section class="cta"><div class="container"><span class="kicker">BẮT ĐẦU TỪ MỘT LỰA CHỌN</span><h2>Tìm sản phẩm dành cho bạn</h2><p>Khám phá bộ sưu tập hoặc kết nối với đội ngũ Aurora.</p><a class="button white" href="<?php echo esc_url(home_url('/lien-he/')); ?>">Liên hệ Aurora ↗</a></div></section>
<?php get_footer(); ?>
