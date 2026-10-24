<?php
defined('ABSPATH') || exit;
add_action('after_setup_theme', function () {
    add_theme_support('title-tag'); add_theme_support('post-thumbnails'); add_theme_support('html5',['search-form','gallery','caption']);
});
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('aurora',get_template_directory_uri().'/assets/site.css',[],filemtime(__DIR__.'/assets/site.css'));
    wp_enqueue_script('aurora',get_template_directory_uri().'/assets/site.js',[],filemtime(__DIR__.'/assets/site.js'),true);
});
function aurora_price($id=null) { return number_format((int)get_post_meta($id?:get_the_ID(),'aurora_price',true),0,',','.').' ₫'; }
function aurora_card() { ?>
<article class="product-card">
  <a class="product-photo" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium_large',['loading'=>'lazy']); ?></a>
  <div class="product-body"><span class="sku"><?php echo esc_html(get_post_meta(get_the_ID(),'aurora_sku',true)); ?></span>
  <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
  <p><?php echo esc_html(get_the_excerpt()); ?></p>
  <div class="product-bottom"><strong><?php echo esc_html(aurora_price()); ?></strong><a href="<?php the_permalink(); ?>">Khám phá <span aria-hidden="true">↗</span></a></div></div>
</article>
<?php }
