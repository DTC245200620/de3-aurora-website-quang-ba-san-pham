<?php
/**
 * Plugin Name: Aurora Products
 * Description: Quản lý và khởi tạo danh mục sản phẩm Aurora.
 * Version: 1.0.0
 * Author: Ngô Thị Như Quỳnh
 */
defined('ABSPATH') || exit;
add_action('init', function () {
    register_post_type('aurora_product', [
        'labels' => ['name'=>'Sản phẩm', 'singular_name'=>'Sản phẩm', 'add_new_item'=>'Thêm sản phẩm', 'edit_item'=>'Sửa sản phẩm'],
        'public'=>true, 'has_archive'=>true, 'rewrite'=>['slug'=>'san-pham'],
        'show_in_rest'=>true, 'menu_icon'=>'dashicons-products',
        'supports'=>['title','editor','excerpt','thumbnail'],
    ]);
});
add_filter('use_block_editor_for_post_type', function ($use, $type) { return $type === 'aurora_product' ? false : $use; }, 10, 2);
function aurora_event($event, $level = 'info', $fields = []) {
    $entry = array_merge(['time'=>gmdate('c'), 'level'=>$level, 'event'=>$event], $fields);
    error_log(wp_json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL, 3, '/var/log/aurora/events.log');
}
add_action('wp_login_failed', function () { aurora_event('login_failed', 'warning'); });
add_action('save_post_aurora_product', function ($id, $post, $update) {
    if (wp_is_post_revision($id) || wp_is_post_autosave($id)) return;
    aurora_event($update ? 'product_updated' : 'product_created', 'info', ['product_id'=>$id]);
}, 20, 3);
add_action('before_delete_post', function ($id) {
    if (get_post_type($id)==='aurora_product') aurora_event('product_deleted', 'info', ['product_id'=>$id]);
});
add_action('add_meta_boxes_aurora_product', function () {
    add_meta_box('aurora_details', 'Thông tin sản phẩm', function ($post) {
        wp_nonce_field('aurora_save', 'aurora_nonce');
        echo '<p><label for="aurora_price">Giá niêm yết (VNĐ)</label><input class="widefat" id="aurora_price" name="aurora_price" type="number" min="0" step="1000" value="'.esc_attr(get_post_meta($post->ID,'aurora_price',true)).'"></p>';
        echo '<p><label for="aurora_sku">Mã sản phẩm</label><input class="widefat" id="aurora_sku" name="aurora_sku" maxlength="50" value="'.esc_attr(get_post_meta($post->ID,'aurora_sku',true)).'"></p>';
        echo '<p>Chọn hình sản phẩm trong mục <strong>Ảnh đại diện</strong>. Mô tả ngắn dùng mục Tóm tắt.</p>';
    }, 'aurora_product');
});
add_action('save_post_aurora_product', function ($id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post',$id) || !isset($_POST['aurora_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['aurora_nonce'])),'aurora_save')) return;
    if (isset($_POST['aurora_price'])) update_post_meta($id,'aurora_price',max(0,(int)$_POST['aurora_price']));
    if (isset($_POST['aurora_sku'])) update_post_meta($id,'aurora_sku',substr(sanitize_text_field(wp_unslash($_POST['aurora_sku'])),0,50));
});
function aurora_seed() {
    if (get_option('aurora_seed_v1')) return;
    require_once ABSPATH.'wp-admin/includes/file.php';
    require_once ABSPATH.'wp-admin/includes/media.php';
    require_once ABSPATH.'wp-admin/includes/image.php';
    $items = [
        ['Bình giữ nhiệt Aurora','binh-giu-nhiet-aurora',329000,'Bền bỉ mỗi ngày, giữ trọn nhiệt độ yêu thích. Dung tích 600 ml với thiết kế tối giản.','bottle'],
        ['Tai nghe Nova','tai-nghe-nova',1290000,'Đắm chìm trong âm nhạc với tai nghe không dây, âm thanh cân bằng và thiết kế êm ái.','headphones'],
        ['Sạc không dây Zen','sac-khong-day-zen',490000,'Góc làm việc gọn gàng hơn với giải pháp sạc không dây nhỏ gọn và tiện lợi.','charger'],
        ['Bàn phím cơ Alpha','ban-phim-co-alpha',890000,'Từng thao tác chính xác, từng ý tưởng liền mạch. Bàn phím cơ cho góc làm việc của bạn.','keyboard'],
        ['Chuột không dây M5','chuot-khong-day-m5',450000,'Chuyển động linh hoạt, cầm nắm thoải mái. Người bạn đồng hành cho ngày làm việc hiệu quả.','mouse'],
        ['Loa Bluetooth Echo','loa-bluetooth-echo',690000,'Mang âm nhạc đến mọi không gian với âm thanh sống động trong một thiết kế nhỏ gọn.','speaker'],
    ];
    foreach ($items as $i=>$item) {
        $existing=get_page_by_path($item[1],OBJECT,'aurora_product');
        if ($existing) continue;
        $id=wp_insert_post(['post_type'=>'aurora_product','post_status'=>'publish','post_title'=>$item[0],'post_name'=>$item[1],'post_excerpt'=>$item[3],'post_content'=>'<p>'.esc_html($item[3]).'</p><p>Aurora lựa chọn những sản phẩm thiết thực, dễ sử dụng và phù hợp với nhịp sống hiện đại. Liên hệ đội ngũ Aurora để được tư vấn thêm về sản phẩm.</p>','menu_order'=>$i],true);
        if (is_wp_error($id)) throw new RuntimeException($id->get_error_message());
        update_post_meta($id,'aurora_price',$item[2]); update_post_meta($id,'aurora_sku',sprintf('AUR-%03d',$i+1));
        $source=__DIR__.'/assets/'.$item[4].'.jpg';
        if (!file_exists($source)) throw new RuntimeException('Missing seed image: '.$item[4]);
        $tmp=wp_tempnam($item[4].'.jpg'); copy($source,$tmp);
        $attachment=media_handle_sideload(['name'=>$item[4].'.jpg','tmp_name'=>$tmp],$id,$item[0]);
        if (is_wp_error($attachment)) throw new RuntimeException($attachment->get_error_message());
        set_post_thumbnail($id,$attachment);
    }
    $pages=[
        'trang-chu'=>['Aurora',''],
        'gioi-thieu'=>['Về Aurora','Aurora mang đến những sản phẩm hữu ích cho công việc và cuộc sống. Chúng tôi tin rằng thiết kế đẹp bắt đầu từ sự đơn giản, chất lượng và trải nghiệm hàng ngày. Mỗi sản phẩm được lựa chọn để đồng hành cùng phong cách riêng của bạn.'],
        'lien-he'=>['Liên hệ Aurora','Bạn cần thêm thông tin về sản phẩm? Đội ngũ Aurora sẵn sàng hỗ trợ bạn lựa chọn sản phẩm phù hợp.'],
    ];
    foreach ($pages as $slug=>$page) if (!get_page_by_path($slug)) wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_name'=>$slug,'post_title'=>$page[0],'post_content'=>$page[1]]);
    update_option('show_on_front','page'); update_option('page_on_front',get_page_by_path('trang-chu')->ID);
    update_option('blogdescription','Sản phẩm cho phong cách của bạn');
    update_option('aurora_seed_v1',1,false);
    flush_rewrite_rules();
    aurora_event('catalog_initialized','info',['products'=>6]);
}
