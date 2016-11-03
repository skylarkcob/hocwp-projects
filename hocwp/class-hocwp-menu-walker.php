<?php
if ( ! function_exists( 'add_filter' ) ) {
	exit;
}

class HOCWP_Menu_Walker extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$menu_title      = $item->title;
		$menu_title_slug = hocwp_sanitize_file_name( $menu_title );
		$indent          = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes         = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[]       = 'menu-item-' . $item->ID;
		$classes[]       = 'menu-' . $menu_title_slug;
		$class_names     = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names     = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
		$id              = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id              = $id ? ' id="' . esc_attr( $id ) . '"' : '';
		$output .= $indent . '<li' . $id . $class_names;
		$atts = apply_filters( 'hocwp_menu_item_attributes', '', $item, $depth, $args );
		$atts = hocwp_attribute_to_string( $atts );
		hocwp_add_string_with_space_before( $output, $atts );
		$output .= '>';
		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';
		$atts           = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
		$attributes     = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
		$item_output = $args->before;
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $args->link_before;
		$item_output = apply_filters( 'hocwp_menu_item_output_link_text_before', $item_output, $item, $args, $depth );
		$link_text   = apply_filters( 'the_title', $item->title, $item->ID );
		$item_output .= apply_filters( 'hocwp_menu_item_output_link_text', $link_text, $item, $args, $depth );
		if ( ! empty( $item->description ) ) {
			$item_output .= '<span class="description">' . $item->description . '</span>';
		}
		$item_output = apply_filters( 'hocwp_menu_item_output_link_text_after', $item_output, $item, $args, $depth );
		$item_output .= $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;
		$item_output = apply_filters( 'hocwp_menu_item_output_link_after', $item_output, $item, $args, $depth );
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent     = str_repeat( "\t", $depth );
		$menu_class = apply_filters( 'hocwp_submenu_class', '', $depth, $args );
		hocwp_add_string_with_space_before( $menu_class, 'sub-menu child-menu' );
		$ul   = '<ul class="' . $menu_class . '"';
		$atts = apply_filters( 'hocwp_submenu_attributes', '', $depth, $args );
		$atts = hocwp_attribute_to_string( $atts );
		hocwp_add_string_with_space_before( $ul, $atts );
		$ul .= '>';
		$sub_menu_start = "\n$indent$ul\n";
		$sub_menu_start = apply_filters( 'hocwp_submenu_start', $sub_menu_start, $depth, $args );
		$output .= $sub_menu_start;
	}

	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$html = apply_filters( 'hocwp_menu_end_el_html', '', $item, $depth, $args );
		hocwp_add_string_with_space_before( $html, "</li>\n" );
		//$html = apply_filters( 'hocwp_menu_end_el_html_after', $html, $item, $depth, $args );
		$output .= $html;
	}
}