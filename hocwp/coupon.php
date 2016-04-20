<?php
function hocwp_coupon_store_base() {
	$option = get_option('hocwp_permalink');
	$base = hocwp_get_value_by_key($option, 'coupon_store_base', 'store');
	$base = apply_filters('hocwp_coupon_store_base', $base);
	if(empty($base)) {
		$base = 'store';
	}
	return $base;
}

function hocwp_coupon_category_base() {
	$option = get_option('hocwp_permalink');
	$base = hocwp_get_value_by_key($option, 'coupon_category_base', 'coupon-cat');
	$base = apply_filters('hocwp_coupon_category_base', $base);
	if(empty($base)) {
		$base = 'coupon-cat';
	}
	return $base;
}

function hocwp_coupon_tag_base() {
	$option = get_option('hocwp_permalink');
	$base = hocwp_get_value_by_key($option, 'coupon_tag_base', 'coupon-tag');
	$base = apply_filters('hocwp_coupon_tag_base', $base);
	if(empty($base)) {
		$base = 'coupon-tag';
	}
	return $base;
}

function hocwp_coupon_type_base() {
	$option = get_option('hocwp_permalink');
	$base = hocwp_get_value_by_key($option, 'coupon_type_base', 'coupon-type');
	$base = apply_filters('hocwp_coupon_type_base', $base);
	if(empty($base)) {
		$base = 'coupon-type';
	}
	return $base;
}

function hocwp_coupon_install_post_type_and_taxonomy() {
	$args = array(
		'name' => __('Coupons', 'hocwp'),
		'singular_name' => __('Coupon', 'hocwp'),
		'supports' => array('editor', 'comments', 'thumbnail'),
		'slug' => 'coupon',
		'taxonomies' => array('store', 'coupon_cat', 'coupon_tag', 'coupon_type'),
		'show_in_admin_bar' => true
	);
	hocwp_register_post_type($args);

	$args = array(
		'name' => __('Events', 'hocwp'),
		'singular_name' => __('Event', 'hocwp'),
		'supports' => array('editor', 'comments', 'thumbnail'),
		'slug' => 'event',
		'show_in_admin_bar' => true
	);
	hocwp_register_post_type($args);

	$args = array(
		'name' => __('Stores', 'hocwp'),
		'singular_name' => __('Store', 'hocwp'),
		'taxonomy' => 'store',
		'slug' => hocwp_coupon_store_base(),
		'post_types' => array('coupon')
	);
	hocwp_register_taxonomy($args);

	$args = array(
		'name' => __('Coupon Categories', 'hocwp'),
		'singular_name' => __('Coupon Category', 'hocwp'),
		'menu_name' => __('Categories', 'hocwp'),
		'slug' => hocwp_coupon_category_base(),
		'taxonomy' => 'coupon_cat',
		'post_types' => array('coupon')
	);
	hocwp_register_taxonomy($args);

	$args = array(
		'name' => __('Coupon Tags', 'hocwp'),
		'singular_name' => __('Coupon Tag', 'hocwp'),
		'menu_name' => __('Tags', 'hocwp'),
		'slug' => hocwp_coupon_tag_base(),
		'taxonomy' => 'coupon_tag',
		'hierarchical' => false,
		'post_types' => array('coupon')
	);
	hocwp_register_taxonomy($args);

	$args = array(
		'name' => __('Coupon Types', 'hocwp'),
		'singular_name' => __('Coupon Type', 'hocwp'),
		'menu_name' => __('Types', 'hocwp'),
		'slug' => hocwp_coupon_type_base(),
		'taxonomy' => 'coupon_type',
		'post_types' => array('coupon')
	);
	hocwp_register_taxonomy($args);
}

function hocwp_get_coupon_url($post_id = null) {
	if(!hocwp_id_number_valid($post_id)) {
		$out = get_query_var('out');
		if(!empty($out)) {
			if(hocwp_id_number_valid($out)) {
				$post_id = $out;
			} else {
				$post = hocwp_get_post_by_slug($out);
				if(is_a($post, 'WP_Post')) {
					$post_id = $post->ID;
				}
			}
		} else {
			$post = hocwp_get_post_by_slug($post_id);
			if(is_a($post, 'WP_Post')) {
				$post_id = $post->ID;
			} else {
				$post_id = get_the_ID();
			}
		}
	}
	$url = hocwp_get_coupon_meta('url', $post_id);
	if(empty($url)) {
		$store = hocwp_get_coupon_store($post_id);
		if(is_a($store, 'WP_Term')) {
			$url = hocwp_get_store_url($store->term_id);
		}
	}
	return $url;
}

function hocwp_get_store_url($id) {
	return get_term_meta($id, 'site', true);
}

function hocwp_get_top_store_by_coupon_count($args = array()) {
	return hocwp_term_get_by_count('store', $args);
}

function hocwp_get_top_category_by_coupon_count($args = array()) {
	return hocwp_term_get_by_count('coupon_cat', $args);
}

function hocwp_get_coupon_categories($args = array()) {
	return get_terms('coupon_cat', $args);
}

function hocwp_get_coupon_stores($args = array()) {
	return get_terms('store', $args);
}

function hocwp_get_coupon_hint($post_id = null) {
	$code = hocwp_get_coupon_code($post_id);
	$len = strlen($code);
	if($len > 3) {
		$len = intval($len/2);
	}
	if($len < 3) {
		$len = 3;
	}
	if($len > 10) {
		$len = 10;
	}
	$len = -$len;
	$code = substr($code, $len);
	return $code;
}

function hocwp_get_coupon_code($post_id = null) {
	if(!hocwp_id_number_valid($post_id)) {
		$post_id = get_the_ID();
	}
	$code = hocwp_get_coupon_meta('coupon_code', $post_id);
	if(empty($code)) {
		$code = hocwp_get_coupon_meta('code', $post_id);
	}
	if(empty($code)) {
		$code = hocwp_get_coupon_meta('wpcf-coupon-code', $post_id);
	}
	return $code;
}

function hocwp_get_coupon_meta($meta_key, $post_id = null) {
	return hocwp_get_post_meta($meta_key, $post_id);
}

function hocwp_get_coupon_percent_label($post_id = null) {
	return hocwp_get_coupon_meta('percent_label', $post_id);
}

function hocwp_get_coupon_text_label($post_id = null) {
	return hocwp_get_coupon_meta('text_label', $post_id);
}

function hocwp_get_coupon_expired_date($post_id = null) {
	return hocwp_get_coupon_meta('expired_date', $post_id);
}

function hocwp_get_coupon_type_term($post_id = null) {
	if(!hocwp_id_number_valid($post_id)) {
		$post_id = get_the_ID();
	}
	$result = array(
		'code'
	);
	$terms = wp_get_post_terms($post_id, 'coupon_type');
	$term = current($terms);
	return $term;
}

function hocwp_get_coupon_type_object($type = 'code') {
	$term = new WP_Error();
	switch($type) {
		case 'deal':
			$term = hocwp_get_term_by_slug('deal', 'coupon_type');
			if(!is_a($term, 'WP_Term')) {
				$term = hocwp_get_term_by_slug('sales', 'coupon_type');
			}
			if(!is_a($term, 'WP_Term')) {
				$term = hocwp_get_term_by_slug('promotion', 'coupon_type');
			}
			break;
		default:
			$term = hocwp_get_term_by_slug('promo-codes', 'coupon_type');
			if(!is_a($term, 'WP_Term')) {
				$term = hocwp_get_term_by_slug('promo-code', 'coupon_type');
			}
			if(!is_a($term, 'WP_Term')) {
				$term = hocwp_get_term_by_slug('code', 'coupon_type');
			}
			if(!is_a($term, 'WP_Term')) {
				$term = hocwp_get_term_by_slug('coupon-code', 'coupon_type');
			}
			if(!is_a($term, 'WP_Term')) {
				$term = hocwp_get_term_by_slug('coupon-code', 'coupon_type');
			}
	}
	return $term;
}

function hocwp_coupon_get_store_by_category($category) {
	$args = array(
		'post_type' => 'coupon',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => $category->taxonomy,
				'field' => 'id',
				'terms' => array($category->term_id)
			)
		)
	);
	$query = hocwp_query($args);
	$result = array();
	if($query->have_posts()) {
		while($query->have_posts()) {
			$query->the_post();
			$terms = wp_get_object_terms(get_the_ID(), 'store');
			if(hocwp_array_has_value($terms)) {
				$result = array_merge($result, $terms);
			}
		}
		wp_reset_postdata();
	}
	$result = array_unique($result, SORT_REGULAR);
	return $result;
}

function hocwp_get_event_coupons($event_id, $args = array()) {
	$args['meta_key'] = 'event';
	$args['meta_value_num'] = $event_id;
	$args['post_type'] = 'coupon';
	return hocwp_query($args);
}

function hocwp_get_expired_coupons($args = array()) {
	$timestamp = current_time('timestamp', 0);
	$args['meta_key'] = 'expired_date';
	$args['meta_value'] = $timestamp;
	$args['meta_compare'] = '<';
	$args['meta_type'] = 'numeric';
	$meta_item = array(
		'key' => 'expired_date',
		'value' => $timestamp,
		'compare' => '<'
	);
	if(isset($args['meta_query'])) {
		foreach($args['meta_query'] as $i => $meta) {
			if(hocwp_array_has_value($meta)) {
				foreach($meta as $j => $child_meta) {
					if('key' == $j && 'expired_date' == $child_meta) {
						unset($args['meta_query'][$i]);
					}
				}
			}
		}
	}
	$args = hocwp_query_sanitize_meta_query($meta_item, $args);
	$meta_item = array(
		'key' => 'expired_date',
		'compare' => 'EXISTS'
	);
	$args = hocwp_query_sanitize_meta_query($meta_item, $args);
	$args['meta_query']['relation'] = 'AND';
	$args['expired_coupon'] = true;
	return hocwp_query($args);
}

function hocwp_get_coupon_type($post_id = null) {
	$term = hocwp_get_coupon_type_term($post_id);
	$result = array();
	if(is_a($term, 'WP_Term')) {
		$type = 'code';
		$text = $term->name;
		switch($term->slug) {
			case 'deal':
			case 'online-deal':
			case 'sale':
			case 'sales':
				$type = 'deal';
				$text = 'Deal';
				break;
			case 'in-store-coupons':
			case 'in-store-coupon':
			case 'in-store':
			case 'print':
			case 'printable':
				$type = 'printable';
				$text = 'Printable';
				break;
			default:
				$type = 'code';
				$text = 'Coupon';
		}
		$result[$type] = $text;
	}
	return $result;
}

function hocwp_coupon_label_html($percent, $text, $type) {
	?>
	<div class="coupon-label-context text-center">
		<p class="percent"><?php echo $percent; ?></p>
		<p class="text"><?php echo $text; ?></p>
	</div>
	<div class="coupon-type text-center">
		<span><?php echo $type; ?></span>
	</div>
	<?php
}

function hocwp_coupon_filter_bar_html($args = array()) {
	$term = hocwp_get_value_by_key($args, 'term');
	$posts_per_page = hocwp_get_value_by_key($args, 'posts_per_page', hocwp_get_posts_per_page());
	$code_count = absint(hocwp_get_value_by_key($args, 'code_count'));
	$deal_count = absint(hocwp_get_value_by_key($args, 'deal_count'));
	?>
	<ul data-store="<?php echo $term->term_id; ?>" data-paged="<?php echo hocwp_get_paged(); ?>" data-posts-per-page="<?php echo $posts_per_page; ?>" class="filter">
		<li>
			<a href="#" data-filter="all" class="active">All (<?php echo $term->count; ?>)</a>
		</li>
		<li>
			<a href="#" data-filter="coupon-code">Coupon Codes (<?php echo $code_count; ?>)</a>
		</li>
		<li>
			<a href="#" data-filter="promotion">Deals (<?php echo $deal_count; ?>)</a>
		</li>
	</ul>
	<?php
}

function hocwp_coupon_button_html($args = array()) {
	$post_id = hocwp_get_value_by_key($args, 'post_id', get_the_ID());
	$type = hocwp_get_value_by_key($args, 'type');
	$code_hint = hocwp_get_value_by_key($args, 'code_hint');
	$type_text = hocwp_get_value_by_key($args, 'type_text');
	$out_url = hocwp_get_value_by_key($args, 'out_url', hocwp_get_coupon_out_url($post_id));
	?>
	<a href="#coupon_box_<?php echo $post_id; ?>" data-post-id="<?php echo $post_id; ?>" class="code type-<?php echo $type; ?>" data-out-url="<?php echo $out_url; ?>" data-toggle="modal">
		<span class="cc"><?php echo $code_hint; ?></span>
		<span class="cc-label"><?php printf(__('Get %s', 'hocwp'), $type_text); ?></span>
	</a>
	<?php
}

function hocwp_coupon_button_code_html($args = array()) {
	$post_id = hocwp_get_value_by_key($args, 'post_id', get_the_ID());
	$code = hocwp_get_value_by_key($args, 'code');
	if(empty($code) && hocwp_id_number_valid($post_id)) {
		$code = hocwp_get_coupon_code($post_id);
	}
	if(empty($code)) {
		return;
	}
	$out_url = hocwp_get_value_by_key($args, 'out_url', hocwp_get_coupon_out_url($post_id));
	$button_class = hocwp_get_value_by_key($args, 'button_class');
	hocwp_add_string_with_space_before($button_class, 'copy-button');
	$input_class = hocwp_get_value_by_key($args, 'input_class');
	hocwp_add_string_with_space_before($input_class, 'text');
	?>
	<div class="code clearfix">
		<input class="<?php echo $input_class; ?>" type="text" value="<?php echo $code; ?>" readonly>
		<a class="<?php echo $button_class; ?>" data-clipboard-text="<?php echo $code; ?>" data-out-url="<?php echo $out_url; ?>">Copy</a>
	</div>
	<?php
}

function hocwp_coupon_vote_comment_html($args = array()) {
	$result = hocwp_get_value_by_key($args, 'result');
	$post_id = hocwp_get_value_by_key($args, 'post_id', get_the_ID());
	if(empty($result)) {
		$likes = hocwp_get_post_meta('likes', $post_id);
		$dislikes = hocwp_get_post_meta('dislikes', $post_id);
		$result = hocwp_percentage($likes, $dislikes);
		$result = apply_filters('hocwp_coupon_rating_percentage', $result, $likes, $dislikes);
		$result .= '%';
	}
	?>
	<p class="vote-result" data-post-id="<?php the_ID(); ?>">
		<i class="fa fa-thumbs-o-up"></i>
		<span><?php printf(__('%s Success', 'hocwp'), $result); ?></span>
	</p>
	<?php
	if(comments_open($post_id) || get_comments_number($post_id)) {
		?>
		<p class="add-comment">
			<a href="#add_comment_<?php the_ID(); ?>">
				<i class="fa fa-comments-o"></i> <?php _e('Add a Comment', 'hocwp'); ?>
			</a>
		</p>
		<?php
	}
}

function hocwp_get_coupon_store($post_id = null) {
	if(!hocwp_id_number_valid($post_id)) {
		$post_id = get_the_ID();
	}
	$term = new WP_Error();
	if(has_term('', 'store', $post_id)) {
		$terms = wp_get_post_terms($post_id, 'store');
		$term = current($terms);
	}
	return $term;
}

function hocwp_get_store_out_link($term) {
	if(hocwp_id_number_valid($term)) {
		$term = get_term($term, 'store');
	}
	$url = '';
	if(is_a($term, 'WP_Term')) {
		$url = home_url('go-store/' . $term->slug);
	}
	return $url;
}

function hocwp_get_coupon_out_url($post_id) {
	if(is_a($post_id, 'WP_Post')) {
		$post_id = $post_id->ID;
	}
	$url = home_url('out/' . $post_id);
	return $url;
}

function hocwp_get_store_by_slug($slug) {
	return hocwp_get_term_by_slug($slug, 'store');
}

$hocwp_coupon_site = apply_filters('hocwp_coupon_site', false);

if(!(bool)$hocwp_coupon_site) {
	return;
}

global $pagenow;

if('edit-tags.php' == $pagenow || 'term.php' == $pagenow) {
	hocwp_term_meta_different_name_field(array('store', 'coupon_cat'));
	hocwp_term_meta_thumbnail_field(array('store'));
	$meta = new HOCWP_Meta('term');
	$meta->set_taxonomies(array('store'));
	$meta->add_field(array('id' => 'site', 'label' => __('Store URL', 'hocwp')));
	$meta->init();
}

if('edit.php' == $pagenow || 'post.php' == $pagenow) {
	hocwp_meta_box_post_attribute(array('coupon'));
	$meta = new HOCWP_Meta('post');
	$meta->set_post_types(array('coupon'));
	$meta->set_id('hocwp_coupon_information');
	$meta->set_title(__('Coupon Information', 'hocwp'));
	$meta->add_field(array('id' => 'percent_label', 'label' => __('Percent Label:', 'hocwp')));
	$meta->add_field(array('id' => 'text_label', 'label' => __('Text Label:', 'hocwp')));
	$meta->add_field(array('id' => 'coupon_code', 'label' => __('Code:', 'hocwp')));
	$meta->add_field(array('id' => 'expired_date', 'label' => __('Expires:', 'hocwp'), 'field_callback' => 'hocwp_field_datetime_picker', 'data_type' => 'timestamp', 'min_date' => 0, 'date_format' => 'm/d/Y'));
	$meta->add_field(array('id' => 'url', 'label' => __('URL:', 'hocwp')));
	$meta->init();
}

function hocwp_coupon_on_save_post($post_id) {
	if(!hocwp_can_save_post($post_id)) {
		return;
	}
	$current_post = get_post($post_id);
	if(!has_term('', 'coupon_type', $post_id) && !empty($current_post->post_title)) {
		wp_set_object_terms($post_id, 'Promo Codes', 'coupon_type');
	}
	if('coupon' == $current_post->post_type) {
		$event = hocwp_get_method_value('event');
		update_post_meta($post_id, 'event', $event);
	}
}
add_action('save_post', 'hocwp_coupon_on_save_post');

function hocwp_coupon_update_post_class($classes) {
	global $post;
	if('coupon' == $post->post_type) {
		$post_id = $post->ID;
		$type = hocwp_get_coupon_type($post_id);
		$type = array_search(current($type), $type);
		if(!empty($type)) {
			$classes[] = 'coupon-type-' . $type;
			if('code' == $type) {
				$code = hocwp_get_coupon_code($post_id);
				if(empty($code)) {
					$classes[] = 'coupon-no-code';
				}
			}
		}
	}
	return $classes;
}
add_filter('post_class', 'hocwp_coupon_update_post_class');

function hocwp_coupon_on_init_hook() {
	add_rewrite_endpoint('go-store', EP_ALL);
	add_rewrite_endpoint('out', EP_ALL);
}
add_action('init', 'hocwp_coupon_on_init_hook');

function hocwp_coupon_on_wp_hook() {
	$store = get_query_var('go-store');
	if(!empty($store)) {
		$term = hocwp_get_store_by_slug($store);
		if(is_a($term, 'WP_Term')) {
			$url = hocwp_get_store_url($term->term_id);
			if(!empty($url)) {
				wp_redirect($url);
				exit;
			} else {
				wp_redirect(home_url('/'));
				exit;
			}
		}
	}
	$out = get_query_var('out');
	if(!empty($out)) {
		$url = hocwp_get_coupon_url($out);
		if(!empty($url)) {
			wp_redirect($url);
			exit;
		} else {
			wp_redirect(home_url('/'));
			exit;
		}
	}
}
add_action('wp', 'hocwp_coupon_on_wp_hook');

function hocwp_coupon_pre_get_posts($query) {
	if($query->is_main_query()) {
		if(is_tax('store')) {
			$posts_per_page = apply_filters('hocwp_archive_coupon_posts_per_page', 15);
			$query->set('posts_per_page', $posts_per_page);
		} elseif(is_search()) {
			$query->set('post_type', 'coupon');
		}
		if(is_post_type_archive('coupon') || is_search() || is_tax('store') || is_tax('coupon_cat') || is_tax('coupon_tag')) {
			$exclude_expired = apply_filters('hocwp_exclude_expired_coupon', false);
			if($exclude_expired) {
				$query_vars = $query->query_vars;
				$expired_coupon = (bool)hocwp_get_value_by_key($query_vars, 'expired_coupon');
				if(!$expired_coupon) {
					$meta_query = hocwp_get_value_by_key($query_vars, 'meta_query');
					if(hocwp_array_has_value($meta_query)) {
						foreach($meta_query as $meta) {
							if(hocwp_array_has_value($meta)) {
								foreach($meta as $child_meta) {
									if(hocwp_array_has_value($child_meta)) {
										$key = hocwp_get_value_by_key($child_meta, 'key');
										$value = hocwp_get_value_by_key($child_meta, 'value');
										$compare = hocwp_get_value_by_key($child_meta, 'compare');
										if('expired_date' == $key && is_numeric($value) && '<' == $compare) {
											$expired_coupon = true;
											break;
										}
									}
								}
							}
						}
					}
				}
				if(!$expired_coupon) {
					$current_date_time = hocwp_get_current_date('m/d/Y');
					$timestamp = current_time('timestamp', 0);
					$meta_item = array(
						'relation' => 'OR',
						array(
							'key' => 'expired_date',
							'value' => $timestamp,
							'type' => 'numeric',
							'compare' => '>='
						),
						array(
							'key' => 'expired_date',
							'compare' => 'NOT EXISTS'
						)
					);
					$args = array(
						$meta_item
					);
					$query->set('meta_query', $args);
				}
			}
		}
	}
	return $query;
}
if(!is_admin()) add_action('pre_get_posts', 'hocwp_coupon_pre_get_posts');

function hocwp_coupon_filter_ajax_callback() {
	$result = array(
		'have_posts' => false
	);
	$term = hocwp_get_method_value('term');
	$filter = hocwp_get_method_value('filter');
	if(hocwp_id_number_valid($term)) {
		$posts_per_page = hocwp_get_method_value('posts_per_page');
		$paged = hocwp_get_method_value('paged');
		$args = array(
			'post_type' => 'coupon',
			'posts_per_page' => $posts_per_page,
			'paged' => $paged,
			'tax_query' => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'store',
					'field' => 'id',
					'terms' => array($term)
				)
			)
		);
		$type_object = new WP_Error();
		switch($filter) {
			case 'coupon-code';
				$type_object = hocwp_get_coupon_type_object();
				break;
			case 'promotion':
				$type_object = hocwp_get_coupon_type_object('deal');
				break;
		}
		if(is_a($type_object, 'WP_Term')) {
			$tax_item = array(
				'taxonomy' => 'coupon_type',
				'field' => 'id',
				'terms' => array($type_object->term_id)
			);
			$args = hocwp_query_sanitize_tax_query($tax_item, $args);
		}
		$query = hocwp_query($args);
		$result['have_posts'] = $query->have_posts();
		if($query->have_posts()) {
			$html_data = '';
			while($query->have_posts()) {
				$query->the_post();
				ob_start();
				hocwp_theme_get_loop('archive-coupon');
				$html_data .= ob_get_clean();
			}
			wp_reset_postdata();
			$result['html_data'] = $html_data;
		}
	}
	echo json_encode($result);
	exit;
}
add_action('wp_ajax_hocwp_coupon_filter', 'hocwp_coupon_filter_ajax_callback');
add_action('wp_ajax_nopriv_hocwp_coupon_filter', 'hocwp_coupon_filter_ajax_callback');

function hocwp_coupon_attribute_meta_box_field($meta) {
	if(!is_object($meta)) {
		return;
	}
	global $post;
	$meta_id = $post->post_type . '_attributes';
	$meta_id = hocwp_sanitize_id($meta_id);
	if('coupon' == $post->post_type && $meta->get_id() == $meta_id) {
		$query = hocwp_query(array('post_type' => 'event', 'posts_per_page' => -1));
		$all_option = '<option value=""></option>';
		$selected = get_post_meta($post->ID, 'event', true);
		foreach($query->posts as $qpost) {
			$all_option .= hocwp_field_get_option(array('value' => $qpost->ID, 'text' => $qpost->post_title, 'selected' => $selected));
		}
		$args = array(
			'id' => 'event_chosen',
			'name' => 'event',
			'all_option' => $all_option,
			'value' => $selected,
			'class' => 'widefat',
			'label' => hocwp_uppercase_first_char_only('Event') . ':',
			'placeholder' => __('Choose parent post', 'hocwp')
		);
		hocwp_field_select_chosen($args);
	}
}
add_action('hocwp_post_meta_box_field', 'hocwp_coupon_attribute_meta_box_field');

if('post.php' == $pagenow || 'edit.php' == $pagenow) {
	add_filter('hocwp_use_chosen_select', '__return_true');
}

if('options-permalink.php' == $pagenow || true) {
	$data = get_option('hocwp_permalink');
	$option = new HOCWP_Option('', 'permalink');
	$option->set_parent_slug('options-permalink.php');
	$option->set_update_option(true);
	$option->add_field(array('value' => hocwp_get_value_by_key($data, 'coupon_store_base'), 'id' => 'coupon_store_base', 'title' => __('Coupon store base', 'hocwp'), 'section' => 'optional', 'placeholder' => hocwp_coupon_store_base()));
	$option->add_field(array('value' => hocwp_get_value_by_key($data, 'coupon_category_base'), 'id' => 'coupon_category_base', 'title' => __('Coupon category base', 'hocwp'), 'section' => 'optional', 'placeholder' => hocwp_coupon_category_base()));
	$option->add_field(array('value' => hocwp_get_value_by_key($data, 'coupon_tag_base'), 'id' => 'coupon_tag_base', 'title' => __('Coupon tag base', 'hocwp'), 'section' => 'optional', 'placeholder' => hocwp_coupon_tag_base()));
	$option->add_field(array('value' => hocwp_get_value_by_key($data, 'coupon_type_base'), 'id' => 'coupon_type_base', 'title' => __('Coupon type base', 'hocwp'), 'section' => 'optional', 'placeholder' => hocwp_coupon_type_base()));
	$option->init();
}

function hocwp_coupon_filter_taxonomy_base($base, $taxonomy) {
	switch($taxonomy) {
		case 'store':
			$base = 'store';
			break;
		case 'coupon_cat':
			$base = 'coupon_cat';
			break;
	}
	return $base;
}
add_filter('hocwp_remove_term_base_taxonomy_base', 'hocwp_coupon_filter_taxonomy_base', 10, 2);